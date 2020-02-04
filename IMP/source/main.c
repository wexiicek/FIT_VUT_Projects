/*
 * FIT VUT
 * IMP Project - Heart Rate Measurement
 *
 * Dominik Juriga (xjurig00)
 *
 * original
 *
 * last edit: 22.12.2019
 */
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include "MK60D10.h"
#include "fsl_adc16.h"
#include "fsl_clock.h"
#include "fsl_common.h"
#include "fsl_gpio.h"
#include "fsl_lptmr.h"
#include "fsl_pit.h"
#include "pin_mux.h"

#define SIGNALS_SIZE 20

// Global Variables
static char num_to_display[5]; // String that will be displayed
static uint64_t m_time = 0; // Measure Time
static unsigned int REFRESH_RATE = 4200; //us, equals approximately 60Hz refresh rate per display digit
static float signals[SIGNALS_SIZE]; // Array of sensor readings

// Helper Functions

/*
 * Turn off all the segments and reset all Cx (control) pins
 */
void display_off() {
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_E_GPIO, BOARD_INITPINS_D_SEG_E_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (0));
    GPIO_WritePinOutput(BOARD_INITPINS_D_C1_GPIO, BOARD_INITPINS_D_C1_PIN, (uint8_t) (1));
    GPIO_WritePinOutput(BOARD_INITPINS_D_C2_GPIO, BOARD_INITPINS_D_C2_PIN, (uint8_t) (1));
    GPIO_WritePinOutput(BOARD_INITPINS_D_C3_GPIO, BOARD_INITPINS_D_C3_PIN, (uint8_t) (1));
    GPIO_WritePinOutput(BOARD_INITPINS_D_C4_GPIO, BOARD_INITPINS_D_C4_PIN, (uint8_t) (1));
}

/*
 * Convert result number into a string that will be displayed
 */
void change_number(unsigned int number) {
	sprintf(num_to_display, "%d", number);
}

// /Helper Functions

// Display Functions

/*
 * Display a number onto the segment display
 */
void display_number(void) {

	/*
	 * Since we can only control a single digit at a time,
	 * we will cycle through them and display them at really high speed,
	 * so that the human eye wont we able to notice
	 */
	static int index = 5 - 1; // When calling the function, start from the first digit

	if (++index == 5)
	{
		index = 1;
	}

	display_off();

	switch (index) {
		case 1:
			GPIO_WritePinOutput(BOARD_INITPINS_D_C1_GPIO, BOARD_INITPINS_D_C1_PIN, (uint8_t) (0));
			break;

		case 2:
			GPIO_WritePinOutput(BOARD_INITPINS_D_C2_GPIO, BOARD_INITPINS_D_C2_PIN, (uint8_t) (0));
			break;

		case 3:
			GPIO_WritePinOutput(BOARD_INITPINS_D_C3_GPIO, BOARD_INITPINS_D_C3_PIN, (uint8_t) (0));
			break;

		case 4:
			GPIO_WritePinOutput(BOARD_INITPINS_D_C4_GPIO, BOARD_INITPINS_D_C4_PIN, (uint8_t) (0));
			break;

		default:
			return;
	}

	switch (num_to_display[index - 1] - '0') {
		case 0:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_E_GPIO, BOARD_INITPINS_D_SEG_E_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			break;

		case 1:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			break;

		case 2:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_E_GPIO, BOARD_INITPINS_D_SEG_E_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 3:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 4:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 5:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 6:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_E_GPIO, BOARD_INITPINS_D_SEG_E_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 7:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			break;

		case 8:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_E_GPIO, BOARD_INITPINS_D_SEG_E_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		case 9:
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_A_GPIO, BOARD_INITPINS_D_SEG_A_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_B_GPIO, BOARD_INITPINS_D_SEG_B_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_C_GPIO, BOARD_INITPINS_D_SEG_C_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_D_GPIO, BOARD_INITPINS_D_SEG_D_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_F_GPIO, BOARD_INITPINS_D_SEG_F_PIN, (uint8_t) (1));
			GPIO_WritePinOutput(BOARD_INITPINS_D_SEG_G_GPIO, BOARD_INITPINS_D_SEG_G_PIN, (uint8_t) (1));
			break;

		default:
			return;
	}
}

// /Display Functions

// Signal Filtering Functions

/*
 * High pass filter to filter possible noise
 * Based on
 * https://en.wikipedia.org/wiki/High-pass_filter
 */
float high_pass_filter(const float signal) {
	float result = 0.0;
	static float signal_prev = 0.0, result_prev = 0.0;
	result =  0.99313 * (result_prev + signal - signal_prev);
	signal_prev = signal;
	result_prev = result;
	return result;
}

/*
 * Low pass filter to filter possible noise
 * Based on
 * https://en.wikipedia.org/wiki/Low-pass_filter
 */
float low_pass_filter(const float signal) {
	float result = 0.0;
	static float result_prev = 0.0;
	result = result_prev + 0.02031 * (signal - result_prev);
	result_prev = result;
	return result;
}

/*
 * Apply low and high pass filters on the signal - to eliminate noise
 */
float eliminate_noise(float signal) {
	float temp;
	temp = low_pass_filter(signal);
	temp = high_pass_filter(signal);
	return temp;
}

// /Signal Filtering Functions


// Sensor Functions

/*
 * Return the value from conversion, once its done
 */
float read_from_sensor()
{
	while (!(ADC16_GetChannelStatusFlags(ADC0, 0) & kADC16_ChannelConversionDoneFlag));

	return (float) ADC16_GetChannelConversionValue(ADC0, 0);
}

// /Sensor Functions

/*
 * Push a new value into an array and move the others
 */
void array_push (float number) {
    for (int i = SIGNALS_SIZE - 1; i >= 0; i--) {
        if (i) {
            signals[i] = signals[i-1];
        }
        else {
            signals[i] = number;
        }
    }
}

/*
 * Set all items in signals array to 0
 */
void array_clear() {
	for (unsigned int i = 0; i < SIGNALS_SIZE; i++) {
		signals[i] = 0.0;
	}
}

/*
 * Calculate the average of array values, converted to integer
 */
int array_average () {
	float sum = 0.0;
	int cnt = 0;
	for (int i = 0; i < SIGNALS_SIZE; i++) {
		if (signals[i] > 0.0) {
			sum += signals[i];
			cnt++;
		}

	}

	return cnt >= 6 ? (int) sum / cnt : 0;
}

/*
 * Read signal from the sensor and calculate the current heart rate
 */
int calculate_heart_rate() {
	static bool signal_rising = false;
	float heart_rate = 0.0;
	static float highest = 0.0;

	// Current LPTMR measure
	uint64_t LPTMR_current = (uint64_t) LPTMR_GetCurrentTimerCount(LPTMR0);
	m_time += COUNT_TO_USEC(LPTMR_current, CLOCK_GetFreq(kCLOCK_McgInternalRefClk));

	// Reset LPTMR
	LPTMR_StopTimer(LPTMR0);
	LPTMR_StartTimer(LPTMR0);

	// Retrieve signal from the sensor and clean it
	float noisy_signal = read_from_sensor();
	float signal = eliminate_noise(noisy_signal);

	if (signal_rising) {
		if (signal > 10.0) {
			if (signal > highest) {
				highest = signal;
			}
		}
		else if(highest > 0.0) {
			if (m_time > 0) {
				float temp_time = (float) m_time;
				heart_rate = 60.0 / (temp_time / 1000000.0) - 40;

				//Ignore if the rate is out of limit
				if (heart_rate > 30.0) {
					if (heart_rate < 200.0) {
						array_push(heart_rate);
					}
				}
			}
			signal_rising = false;
			m_time = 0;
		}
	}
	else if (signal <= 0.0) {
		highest = 0.0;
		signal_rising = true;
	}
	return array_average(signals);
}

/*
 * Handle PORTE interruptions..
 * Button SW5 in on PORTE, the button is used to
 *
 */
void PORTE_IRQHandler(void)
{
	array_clear();
	m_time = 0;
	change_number(0);
	GPIO_ClearPinsInterruptFlags(BOARD_INITPINS_FUN_BTN_GPIO, 1U << BOARD_INITPINS_FUN_BTN_PIN);
}

/*
 * Handle PIT interruptions..
 * Refresh the display, when PIT generates an interrupt
 */
void PIT0_IRQHandler(void)
{
	display_number();
	PIT_ClearStatusFlags(PIT, kPIT_Chnl_0, kPIT_TimerFlag);
}


/*
 * Set up the hardware
 * Using:
 * 	PIT - Periodic Interrupt Timer
 * 		- to create interrupts that refresh the display
 *
 *  LPTMR - Low Power Timer Driver
 *  	  - to measure heart rate
 *
 *  ADC16 - 16 bit analog to digital converter
 *  	  - to convert the values from the sensor
 *
 *  pins 14, 19-30 in the P1 array
 */
void initialize_hardware() {
	// Initialize pin setup from pin_mux
	BOARD_InitPins();

	// LPTMR setup based on
	// https://mcuxpresso.nxp.com/api_doc/dev/210/group__lptmr.html
	CLOCK_SetInternalRefClkConfig(kMCG_IrclkEnable, kMCG_IrcSlow, 0);
	CLOCK_EnableClock(kCLOCK_Lptmr0);
	lptmr_config_t lptmrConfig;
	LPTMR_GetDefaultConfig(&lptmrConfig);
	lptmrConfig.prescalerClockSource = kLPTMR_PrescalerClock_0;
	LPTMR_Init(LPTMR0, &lptmrConfig);
	LPTMR_SetTimerPeriod(LPTMR0, 65535);

	// PIT setup based on
	// https://mcuxpresso.nxp.com/api_doc/dev/210/group__pit.html
	CLOCK_EnableClock(kCLOCK_Pit0);
	pit_config_t pitConfig;
	PIT_GetDefaultConfig(&pitConfig);
	PIT_Init(PIT, &pitConfig);
	EnableIRQ(PIT0_IRQn);
	PIT_EnableInterrupts(PIT, kPIT_Chnl_0, kPIT_TimerInterruptEnable);
	PIT_SetTimerPeriod(PIT, kPIT_Chnl_0, USEC_TO_COUNT(REFRESH_RATE, CLOCK_GetFreq(kCLOCK_BusClk)));
	PIT_StartTimer(PIT, kPIT_Chnl_0);

	// ADC setup based on
	// https://mcuxpresso.nxp.com/api_doc/dev/210/group__adc16.html
	CLOCK_EnableClock(kCLOCK_Adc0);
	adc16_config_t adc16ConfigStruct;
	ADC16_GetDefaultConfig(&adc16ConfigStruct);
	adc16ConfigStruct.enableContinuousConversion = true;
#if defined(FSL_FEATURE_ADC16_MAX_RESOLUTION) && FSL_FEATURE_ADC16_MAX_RESOLUTION >= 16
	adc16ConfigStruct.resolution = kADC16_Resolution16Bit;
#endif
	ADC16_Init(ADC0, &adc16ConfigStruct);
	ADC16_EnableHardwareTrigger(ADC0, false);
	ADC16_SetHardwareAverage(ADC0, kADC16_HardwareAverageCount32);

#if defined(FSL_FEATURE_ADC16_HAS_CALIBRATION) && FSL_FEATURE_ADC16_HAS_CALIBRATION
	ADC16_DoAutoCalibration(ADC0);
#endif
	adc16_channel_config_t adc16ChannelConfigStruct = {
		.channelNumber = 0,
		.enableInterruptOnConversionCompleted = false,
	};
#if defined(FSL_FEATURE_ADC16_HAS_DIFF_MODE) && FSL_FEATURE_ADC16_HAS_DIFF_MODE
	adc16ChannelConfigStruct.enableDifferentialConversion = false;
#endif
	ADC16_SetChannelConfig(ADC0, 0, &adc16ChannelConfigStruct);
	EnableIRQ(PORTE_IRQn);
}

/*
 * Start the heart rate measurement..
 * Measure input from the sensor in an infinite loop
 */
void initiate_measurement() {
	while (true) {
		change_number((int) calculate_heart_rate());
	}
}

int main(void)
{
	initialize_hardware();

	array_clear();

	change_number(0);

	initiate_measurement();

	return 0;
}
