library IEEE;
use IEEE.std_logic_1164.all;
use IEEE.std_logic_arith.all;
use IEEE.std_logic_unsigned.all;

entity ledc8x8 is
port(
    ROW: out std_logic_vector(0 to 7); 
    LED: out std_logic_vector(0 to 7);
    SMCLK: in std_logic;
    RESET: in std_logic
);
end ledc8x8;

architecture main of ledc8x8 is

    -- Sem doplnte definice vnitrnich signalu.

    signal switch1: std_logic;
    signal switch2: std_logic;
    signal firstName: std_logic_vector(0 to 7);
    signal lastName: std_logic_vector(0 to 7);
    signal counterOfRows: std_logic_vector(0 to 7);    
    signal empty: std_logic_vector(0 to 7);
    signal ce : std_logic := '0';

begin

    -- Sem doplnte popis obvodu. Doporuceni: pouzivejte zakladni obvodove prvky
    -- (multiplexory, registry, dekodery,...), jejich funkce popisujte pomoci
    -- procesu VHDL a propojeni techto prvku, tj. komunikaci mezi procesy,
    -- realizujte pomoci vnitrnich signalu deklarovanych vyse.

    -- DODRZUJTE ZASADY PSANI SYNTETIZOVATELNEHO VHDL KODU OBVODOVYCH PRVKU,
    -- JEZ JSOU PROBIRANY ZEJMENA NA UVODNICH CVICENI INP A SHRNUTY NA WEBU:
    -- http://merlin.fit.vutbr.cz/FITkit/docs/navody/synth_templates.html.

    -- Nezapomente take doplnit mapovani signalu rozhrani na piny FPGA
    -- v souboru ledc8x8.ucf.
    
    timer: process (SMCLK, RESET, switch1, switch2, ce)
    variable timeDuration: std_logic_vector(15 downto 0);
    variable counter: std_logic_vector(7 downto 0);
    begin

	if (RESET = '1') then
        switch1 <= '0';
        switch2 <= '0';
		counter := "00000000";			
		timeDuration := "0000000000000000";

	elsif ((SMCLK = '1') and (SMCLK'event)) then

		counter := counter + 1; 

		if counter = "11111111" then
			timeDuration := timeDuration + 1;
			ce <= '1';
		else
			ce <= '0';
		end if;  			
		 if (switch1 = '0') then
                if ((switch2 = '0') and (timeDuration = "0001110000100000")) then
                    switch1 <= '1';
                    switch2 <= '0';

                elsif ((switch2 = '1') and (timeDuration = "0101010001100000")) then
                    switch1 <= '1';
                    switch2 <= '1';

                end if;
            
            elsif (switch1 = '1') then
                if ((switch2 = '0') and (timeDuration = "0011100001000000")) then
                    switch1 <= '0';
                    switch2 <= '1';

                elsif ((switch2 = '1') and (timeDuration = "0111000010000000")) then
                    switch1 <= '0';
                    switch2 <= '0';
                    timeDuration := "0000000000000000";

                end if;

            end if;
	end if;       
    end process;	

    --counting rows
    counterOfRowsProc: process (RESET, SMCLK, ce, counterOfRows)
    begin
	if (RESET = '1') then
		counterOfRows <= "10000000";
	elsif (((SMCLK = '1') and (SMCLK'event)) and (ce = '1')) then
			counterOfRows <= counterOfRows(7) & counterOfRows(0 to 6);
	end if;     
	ROW <= counterOfRows;
    end process;

    --showing empty (= nothing)
    emptyDecoder: process (counterOfRows, empty)
    begin
        case counterOfRows is         
            when "10000000" => empty <= "11111111";
            when "01000000" => empty <= "11111111";
            when "00100000" => empty <= "11111111";
            when "00010000" => empty <= "11111111";
            when "00001000" => empty <= "11111111";
            when "00000100" => empty <= "11111111";
            when "00000010" => empty <= "11111111";
            when "00000001" => empty <= "11111111";	
            when others => empty <= "11111111";		
        end case;
    end process;

    --showing first name
    firstNameDecoder: process (counterOfRows, firstName)
    begin
        case counterOfRows is         
            when "10000000" => firstName <= "11111111";
            when "01000000" => firstName <= "00000111";
            when "00100000" => firstName <= "00000011";
            when "00010000" => firstName <= "00111001";
            when "00001000" => firstName <= "00111100";
            when "00000100" => firstName <= "00111001";
            when "00000010" => firstName <= "00000011";
            when "00000001" => firstName <= "00000111";	
            when others => firstName <= "11111111";
        end case;
    end process;
   
    --showing last name
    lastNameDecoder: process (counterOfRows, lastName)
    begin
        case counterOfRows is         
            when "10000000" => lastName <= "11111111";
            when "01000000" => lastName <= "00000000";
            when "00100000" => lastName <= "00000000";
            when "00010000" => lastName <= "11111100";
            when "00001000" => lastName <= "11111100";
            when "00000100" => lastName <= "00111000";
            when "00000010" => lastName <= "10000001";
            when "00000001" => lastName <= "11000011";
            when others => lastName <= "11111111";
        end case;
    end process;

   --multiplexer
   multiplexer: process (SMCLK, RESET, switch1, switch2, firstName, lastName, empty)
   begin

	if (RESET = '1') then
	    LED <= firstName;

    elsif ((SMCLK = '1') and (SMCLK'event)) then
        if (switch1 = '1') then
            LED <= empty;

        elsif (switch1 = '0') then            
                if (switch2 = '0') then
                    LED <= firstName;
                elsif (switch2 = '1') then
                    LED <= lastName;
                end if;
        end if;
	end if;
    end process;

end main;
