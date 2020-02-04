
## Implementační dokumentace k 2. úloze do IPP 2018/2019 <br> Jméno a příjmení: Dominik Juriga <br>Login: xjurig00
### Script Interpret.py
The interpreter expects a XML representation of `IPPcode19`.  `xml.etree.ElementTree` has been used to simplify the workflow with XML files. If an error-free file is provided, the interpreter executes the instructions one by one. 

The source has to contain a header, which is checked at the very beginning of the interpreter. All the instructions are compared against the list of instructions stored in the `instructions` variable. The whole code is then ran through a for loop, which looks for `LABEL` instructions and stores the label names and instruction order into the `labels` variable.

Instructions are treated independently. This is performed by a for loop (located in the `interpreter` function), which loads the current instruction into variable `line`. Syntactical analysis checks if the instruction is valid or not. Instruction arguments are then analyzed, or error is thrown if the latter is true.

Every argument needs to meet certain criteria. A type check is performed for an argument. Following a succesful check, value of the argument is then saved in variable `value_list`. This variable contains data needed for the execution of the current instruction. Each entry consists of argument value and type of value. If the argument value is a variable which is located in either of the available frames, the value from the frame is then retrieved by using the function `retrieve_value`. This variable is initiated as an empty list with every loop iteration. Following all the compulsory checks, the instruction is executed by performing a certain action (e.g. printing a value, adding two values,...)


### Script Test.php
The testing suite for IPPcode19 is built to run a series of tests for `parse.php` and `interpret.py` scripts. The script checks the arguments and sets the iternal variables accordingly (changing the interpreter or parser file, test directory, recursive search into subfolders,...). 

All the files from the specified directory are gathered by `get_files` function. The result of this function is then exploded, which leaves us with the list of test names (and their path). After all this data has been received, a for loop iterates through every test.

The function `run_test` decides which mode to use based on the input. If the mode has not been specified, the testing suite runs the `*.src` files through the parser which produces `*.pres` temporary files. This file is then used as a source for the interpreter, which produces `*.ires` temporary files. A `diff` check with the `*.out` file is performed afterwards. If the mode has been specified, only the parser or the interpreter tests are performed.

Reference return value is retrieved from the `*.rc` file. If the reference return value matches the return value from the test, `JExamXML` and `diff` check is performed for parser and interpreter tests, respectively. If the values match and there are no differences in the output file, the test is considered as `passed` and is printed into the `stdout`. If either of the beforementioned is not the same, the test is considered as `failed` with the respective message. The temporary files are removed after each test.

Test output is stylized into a modern-looking table-like interface. Each test has it's own area, which can be expanded to reveal additional information such as return value, context, etc.