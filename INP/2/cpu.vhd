-- cpu.vhd: Simple 8-bit CPU (BrainF*ck interpreter)
-- Copyright (C) 2018 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): Dominik Juriga (xjurig00@stud.fit.vutbr.cz)
--

library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet ROM
   CODE_ADDR : out std_logic_vector(11 downto 0); -- adresa do pameti
   CODE_DATA : in std_logic_vector(7 downto 0);   -- CODE_DATA <- rom[CODE_ADDR] pokud CODE_EN='1'
   CODE_EN   : out std_logic;                     -- povoleni cinnosti
   
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(9 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- mem[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_RDWR  : out std_logic;                    -- cteni z pameti (DATA_RDWR='1') / zapis do pameti (DATA_RDWR='0')
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA obsahuje stisknuty znak klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna pokud IN_VLD='1'
   IN_REQ    : out std_logic;                     -- pozadavek na vstup dat z klavesnice
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- pokud OUT_BUSY='1', LCD je zaneprazdnen, nelze zapisovat,  OUT_WE musi byt '0'
   OUT_WE   : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is

    -- zde dopiste potrebne deklarace signalu

	signal pcRegister : std_logic_vector (11 downto 0);
	signal pointer : std_logic_vector (9 downto 0);
	signal temporary, counter : std_logic_vector (7 downto 0);
	signal dMultiplexer : std_logic_vector (1 downto 0);
	signal registerIncrease, registerDecrease, pointerIncrease, pointerDecrease : std_logic;
	signal counterIncrease, counterDecrease: std_logic;

	type state is (
		sIdle,
		sWait,
		sGetState,
		sPtrInc,
		sPtrDec,
		sValInc,
		sValDec,
		sValIncNest,
		sValDecNest,
		sWhlBeg,
		sWhlEnd,
		sWhlBegNestTwo,
		sWhlEndNestTwo,
		sWhlBegNestThr,
		sWhlEndNestThr,
		sWhlBegNestFour,
		sWhlEndNestFour,
		sWhlEndNestFive,
		sPChar,
		sPCharTwo,
		sGChar,
		sCmt,
		sCmtTwo,
		sCmtThree,
		sHexNum,
		sHexAlp,
		sWrngChar,
		sStahp
	);


	type typeOfInstruction is (
		iCmt,
		iPtrInc,
		iPtrDec,
		iWhlBeg,
		iWhlEnd,
		iValInc,
		iValDec,
		iPChar,
		iGChar,
		iHexNum,
		iHexAlp,
		iStahp,
		iWrngChar,
		iIdle
	);
	
	signal instruction : typeOfInstruction;
	signal currentState, nextState : state;
begin

	-- zde dopiste vlastni VHDL kod dle blokoveho schema

	-- inspirujte se kodem procesoru ze cviceni

	pcProcess : process(CLK, RESET, pcRegister, registerIncrease, registerDecrease)
	begin
		if RESET = '1' then
			pcRegister <= (others => '0');
		elsif rising_edge(CLK) then
			if registerIncrease = '1' then	
				pcRegister <= pcRegister + 1;
			elsif registerDecrease = '1' then
				pcRegister <= pcRegister - 1;
			end if;
		end if;
	end process pcProcess;

	CODE_ADDR <= pcRegister;

	pointerProcess : process (CLK, RESET, pointer, pointerIncrease, pointerDecrease)
	begin
		if RESET = '1' then
			pointer <= (others => '0');
		elsif rising_edge(CLK) then
			if pointerIncrease = '1' then
				pointer <= pointer + 1;
			elsif pointerDecrease = '1' then
				pointer <= pointer - 1;
			end if;
		end if;
	end process pointerProcess;

	DATA_ADDR <= pointer;

	counterProcess : process(CLK, RESET, counter, counterIncrease, counterIncrease)
	begin
		if RESET = '1' then
			counter <= (others => '0');
		elsif rising_edge(CLK) then
			if counterIncrease = '1' then
				counter <= counter + 1;
			elsif counterDecrease = '1' then
				counter <= counter - 1;
			end if;
		end if;
	end process counterProcess;

	sGetStateProcess : process (CODE_DATA)
	begin
		case(CODE_DATA) is
        when X"00" => instruction <= iStahp;
        when X"2B" => instruction <= iValInc;
        when X"2C" => instruction <= iGChar;
        when X"2D" => instruction <= iValDec;
        when X"2E" => instruction <= iPChar;
        when X"3C" => instruction <= iPtrDec;
        when X"3E" => instruction <= iPtrInc;
        when X"5B" => instruction <= iWhlBeg;
        when X"5D" => instruction <= iWhlEnd;
        when X"23" => instruction <= iCmt;
        when X"30" => instruction <= iHexNum;
        when X"31" => instruction <= iHexNum;
        when X"32" => instruction <= iHexNum;
        when X"33" => instruction <= iHexNum;
        when X"34" => instruction <= iHexNum;
        when X"35" => instruction <= iHexNum;
        when X"36" => instruction <= iHexNum;
        when X"37" => instruction <= iHexNum;
        when X"38" => instruction <= iHexNum;
        when X"39" => instruction <= iHexNum;
        when X"41" => instruction <= iHexAlp;
        when X"42" => instruction <= iHexAlp;
        when X"43" => instruction <= iHexAlp;
        when X"44" => instruction <= iHexAlp;
        when X"45" => instruction <= iHexAlp;
        when X"46" => instruction <= iHexAlp;
        when others => instruction <= iWrngChar;
      end case;
	end process sGetStateProcess;

	multiplexerProcess : process (RESET, dMultiplexer, IN_DATA, DATA_RDATA)
	begin
		if RESET = '1' then
			DATA_WDATA <= (others => '0');
		else
			if dMultiplexer = "00" then
				DATA_WDATA <= IN_DATA;
			elsif dMultiplexer = "10" then
				DATA_WDATA <= DATA_RDATA - "00000001";
			elsif dMultiplexer = "01" then
				DATA_WDATA <= DATA_RDATA + "00000001";
			elsif dMultiplexer = "11" then
				DATA_WDATA <= temporary;
			end if;
		end if;
	end process multiplexerProcess;

	currentStateProcess : process(CLK, RESET)
	begin
		if (RESET = '1') then
			currentState <= sIdle;
		elsif rising_edge(CLK) then
			if EN = '1' then
				currentState <= nextState;
				end if;
		end if;
	end process currentStateProcess;

	

	FSMProcess : process (CLK, RESET, EN, CODE_DATA, IN_VLD, IN_DATA, DATA_RDATA, OUT_BUSY, currentState, instruction, counter, dMultiplexer)
	begin
		dMultiplexer <= "00";
		counterIncrease <= '0';
		counterDecrease <= '0';
		pointerIncrease <= '0';
		pointerDecrease <= '0';
		registerIncrease <= '0';
		registerDecrease <= '0';		
		DATA_EN <= '0';
		DATA_RDWR <= '0';
		CODE_EN <= '1';
		OUT_WE <= '0';
		IN_REQ <= '0';
		nextState <= sIdle;

		case currentState is

			when sIdle => nextState <= sWait;

			when sWait => nextState <= sGetState;
			CODE_EN <= '1';

			when sGetState =>
				if instruction = iPtrInc then
					nextState <= sPtrInc;
				elsif instruction = iPtrDec then
					nextState <= sPtrDec;
				elsif instruction = iValInc then
					nextState <= sValInc;
				elsif instruction = iValDec then
					nextState <= sValDec;
				elsif instruction = iWhlBeg then
					nextState <= sWhlBeg;
				elsif instruction = iWhlEnd then
					nextState <= sWhlEnd;
				elsif instruction = iPChar then
					nextState <= sPChar;
				elsif instruction = iGChar then
					nextState <= sGChar;
				elsif instruction = iCmt then
					nextState <= sCmt;
				elsif instruction = iHexNum then
					nextState <= sHexNum;
				elsif instruction = iHexAlp then
					nextState <= sHexAlp;
				elsif instruction = iStahp then
					nextState <= sStahp;
				else nextState <= sWrngChar;
				end if;

			when sPtrInc =>
				registerIncrease <= '1';
				pointerIncrease <= '1';
				nextState <= sWait;
			
			when sPtrDec =>
				registerIncrease <= '1';
				pointerDecrease <= '1';
				nextState <= sWait;

			when sValInc =>
				DATA_RDWR <= '1';
				DATA_EN <= '1';
				nextState <= sValIncNest;

			when sValIncNest =>
				dMultiplexer <= "01";
				DATA_RDWR <= '0';
				DATA_EN <= '1';
				registerIncrease <= '1';
				nextState <= sWait;

			when sValDec =>
				DATA_RDWR <= '1';
				DATA_EN <= '1';
				nextState <= sValDecNest;

			when sValDecNest =>
				dMultiplexer <= "10";
				DATA_RDWR <= '0';
				DATA_EN <= '1';
				registerIncrease <= '1';
				nextState <= sWait;

     		when sWhlBeg =>
        		registerIncrease <= '1';
        		DATA_RDWR <= '1';
        		DATA_EN <= '1';
        		nextState <= sWhlBegNestTwo;

      		when sWhlBegNestTwo =>
        		if (DATA_RDATA = "00000000") then
          			counterIncrease <= '1';
          			nextState  <= sWhlBegNestThr;
        		else
          			nextState  <= sWait;			
		        end if;

      		when sWhlBegNestThr =>
        		if (counter = "00000000") then
          			nextState <= sWait;
        		else
          			CODE_EN <= '1';
          			nextState <= sWhlBegNestFour;
        		end if;

     		when sWhlBegNestFour => 
        		if instruction = iWhlBeg then
          			counterIncrease	<= '1';
        		elsif instruction = iWhlEnd then
          			counterDecrease	<= '1';
        		end if;
        		registerIncrease <= '1';
        		nextState <= sWhlBegNestThr;

      		when sWhlEnd =>
        		DATA_RDWR <= '1';
		        DATA_EN	<= '1';
		        nextState <= sWhlEndNestTwo;

      		when sWhlEndNestTwo =>
        		if (DATA_RDATA = "00000000") then
          			registerIncrease  <= '1';
          			nextState	<= sWait;
        		else
          			registerDecrease	<= '1';
          			counterIncrease	<= '1';
          			nextState	<= sWhlEndNestThr;
        		end if;

      		when sWhlEndNestThr =>
        		if counter = "00000000" then
          			nextState <= sWait;
        	else
          		CODE_EN	<= '1';
          		nextState <= sWhlEndNestFour;
        	end if;

			when sWhlEndNestFour =>
				if instruction = iWhlEnd then
					counterIncrease	<= '1';
				elsif instruction = iWhlBeg then		
					counterDecrease	<= '1';
				end if;
				nextState <= sWhlEndNestFive;

			when sWhlEndNestFive =>
				if counter = "00000000" then 
					registerIncrease <= '1';
				else 
					registerDecrease <= '1';
				end if;
				nextState <= sWhlEndNestThr;
			
			when sPChar =>
				if OUT_BUSY = '1' then
					nextState <= sPChar;
				else 
					DATA_EN <= '1';
					DATA_RDWR <= '1';
					nextState <= sPCharTwo;
				end if;

			when sPCharTwo =>
				registerIncrease <= '1';
				OUT_WE <= '1';
				OUT_DATA <= DATA_RDATA;
				nextState <= sWait;

			when sGChar =>
				nextState <= sGChar;
				IN_REQ <= '1';		
				if IN_VLD = '1' then
					dMultiplexer	<= "00";
					registerIncrease <= '1';
					DATA_EN	<= '1';
					DATA_RDWR <= '0';
					IN_REQ <= '0'; 
					nextState <= sWait;
				end if;
			
			when sCmt =>
				registerIncrease <= '1';
				nextState <= sCmtTwo;

			when sCmtTwo =>
				CODE_EN <= '1';
				nextState <= sCmtThree;

			when sCmtThree =>
				if instruction = iCmt then
					registerIncrease <= '1';
					nextState <= sWait;
				else
					nextState <= sCmt;
				end if;

			when sHexNum =>
				dMultiplexer <= "11";
				DATA_EN <= '1';
				registerIncrease <= '1';
				temporary <= CODE_DATA(3 downto 0) & "0000";
				nextState <= sWait;
 
			when sHexAlp =>
				dMultiplexer <= "11";
				temporary <= (CODE_DATA(3 downto 0) + std_logic_vector(conv_unsigned(9, temporary'LENGTH))) & "0000";
				registerIncrease <= '1';
				DATA_EN <= '1';
				nextState <= sWait;

			when sStahp =>
				nextState <= sStahp;

			when sWrngChar =>
				registerIncrease <= '1';
				nextState <= sWait;

    	end case;
	end process FSMProcess;


end behavioral;
 
