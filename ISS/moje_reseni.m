% ISS Project 2018/19
% Dominik Juriga 2BIT
% xjurig00@stud.fit.vutbr.cz

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 1

[signal, fs] = audioread('xjurig00.wav');
signal = signal';
len = length(signal);
tLen = len / fs;
symCount = len / 16;
plot(signal);
fprintf('Uloha 1:\n');
fprintf('Vzorkovacia frekvencia: %f [Hz]. Dlzka v sekundach: %f [s]. \nDlzka vo vzorkach: %f. Pocet binarnych symbolov %f.\n', fs, tLen, len, symCount);

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 2

graph = figure('Visible', 'off');
dt = 1/fs;
t = 0:dt:(len*dt)-dt;
hold on;
plot(t, signal, 'r');
xlabel('t');
ylabel('s[n], symbols');
axis([-0.0001 0.020001, -1 1]);
for i = 8:16:len
  if i < 321
  if signal(i) > 0
    stem((i*dt), 1, 'o');
  else
    stem((i*dt), 0, 'o');
  endif
  endif
endfor
hold off;
print(graph, '2.svg');
close(graph);

fprintf('Uloha 2: Hotovo.\n');


%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 3

B = [0.0192 -0.0185 -0.0185 0.0192];
A = [1.0000 -2.8870 2.7997 -0.9113];

%Pokial je hodnota korenov vacsia ako 1, znamena to ze je
%filter nestabilny
if (abs(roots(A))) > 1
  fprintf('    Filter nie je stabilny.\n');
else
  fprintf('    Filter je stabilny.\n');
endif

%Vytvorime graf a ulozime ho do suboru 3.svg
graph = figure('Visible', 'off');
zplane(B, A);
xlabel('Realna osa'); ylabel('Imaginarna osa');
print(graph, '3.svg');
close(graph);
fprintf('Uloha 3: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 4
graph = figure('Visible', 'off');
plot((0:255) / 256 *fs / 2, abs(freqz(B, A, 256)));
temp = abs(freqz(B, A, 256));
xlabel('f [Hz]'); ylabel('|H(f)|');
grid;
print(graph, '4.svg');
close(graph);
fprintf('Uloha 4: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 5

shiftCount = 14;
graph = figure('Visible', 'off');
hold on
signalFiltered = filter(B, A, signal);
signalShifted = shift(signalFiltered, [-shiftCount]);
plot(t, signalFiltered, 'r');
plot(t, signal, 'g');
plot(t, signalShifted, 'b');
axis([-0.00001 0.020001,-1 1]);
xlabel('t [s]');
ylabel('s[n], ss[n], ss_s_h_i_f_t_e_d[n]');
grid;
hold off;
print(graph, '5.svg');
close(graph);

fprintf('Uloha 5: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 6

graph = figure('Visible', 'off');
hold on
plot(t, signal, 'r');
plot(t, signalFiltered, 'g');
plot(t, signalShifted, 'b');
xlabel('t');
ylabel('s[n], ss[n], ss_s_h_i_f_t_e_d[n], symbols');
axis([-0.00001 0.020001,-1 1]);
for i = 8:16:len
  if i < 321
  if signalShifted(i) > 0
    stem((i*dt), 1, 'o');
  else
    stem((i*dt), 0, 'o');
  endif
  endif
endfor
print(graph, '6.svg');
close(graph);
hold off;
fprintf('Uloha 6: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 7

fprintf('Uloha 7: Prebieha vypocet\n');
wrong = 0; all = 0;
decode = zeros(len-shiftCount, 1);
decodeShifted = zeros(len-shiftCount, 1);
for i = 8:16:len-shiftCount
  all = all + 1;
  if signal(i) > 0
    decode(i) = 1;
  else
    decode(i) = 0;
  endif
  
  if signalShifted(i) > 0
    decodeShifted(i) = 1;
  else
    decodeShifted(i) = 0;
  endif
  
  xorRes = xor(decode(i), decodeShifted(i));
  if xorRes > 0
    wrong = wrong + 1;
  endif
    
    
end
fprintf('    Pocet chyb: %d\n    Chybovost: %f%%\n', counter, ((wrong/all))*100);
fprintf('Uloha 7: Hotovo\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%uloha 8

fMax = fs/2;
graph = figure('Visible', 'off');
fourSpect = abs(fft(signal) (1:fMax));
plot(fourSpect, 'r');
hold on;
fourSpectShifted = abs(fft(signalFiltered) (1:fMax));
plot(fourSpectShifted, 'g');
xlabel('[Hz]');
hold off;
print(graph, '8.svg');
close(graph);
fprintf('Uloha 8: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 9

graph = figure('Visible', 'off');
set(gca,'XTick',[0 1  2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20] );
set(gca,'XTickLabel',[-1.0, -0.9, -0.8, -0.7, -0.6, -0.5, -0.4, -0.3, -0.2, -0.1, 0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0] );
plot((hist(signal, 20))/fs);
print(graph, '9.svg');
close(graph);
fprintf('Uloha 9: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 10

graph = figure('Visible', 'off');
R = xcorr(signal) / (len);
k = (-50:50);
R = R(k + len);
xlabel('k');
hold on;
plot(xcorr(signal, 50, 'biased'));
hold off;
set(gca, 'XTickLabel', [-50, -30, -10, 10, 30, 50, 70]);
print(graph, '10.svg');
close(graph);

fprintf('Uloha 10: Subor bol vygenerovany.\n');

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

%Uloha 11
fprintf('Uloha 11: Prebieha vypocet\n');
fprintf('    Hodnota koeficientov:\n    R[0] = %f\n    R[1] = %f\n    R[16] = %f\n',R(50), R(51), R(66));
fprintf('Uloha 11: Hotovo\n');





