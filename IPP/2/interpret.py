import sys
import getopt
import xml.etree.ElementTree as xmlTree
import re

debug = 0

instructions = [
        ['MOVE', 2],
        ['CREATEFRAME', 0],
        ['PUSHFRAME', 0],
        ['POPFRAME', 0],
        ['DEFVAR', 1],
        ['CALL', 1],
        ['RETURN', 0],
        ['PUSHS', 1],
        ['POPS', 1],
        ['ADD', 3],
        ['SUB', 3],
        ['MUL', 3],
        ['IDIV', 3],
        ['LT', 3],
        ['GT', 3],
        ['EQ', 3],
        ['AND', 3],
        ['OR', 3],
        ['NOT', 2],
        ['INT2CHAR',2],
        ['STRI2INT', 3],
        ['READ', 2],
        ['WRITE', 1],
        ['CONCAT', 3],
        ['STRLEN', 2],
        ['GETCHAR', 3],
        ['SETCHAR', 3],
        ['TYPE', 2],
        ['LABEL', 1],
        ['JUMP', 1],
        ['JUMPIFEQ', 3],
        ['JUMPIFNEQ', 3],
        ['EXIT', 1],
        ['DPRINT', 1],
        ['BREAK', 1],
]

errorType = [
    [10, "Invalid arguments."],                         # 0
    [11, "Error while opening input files."],           # 1
    [31, "Incorrect XML input."],                       # 2
    [32, "Lexical error in source code."],              # 3
    [52, "Semantical error."],                          # 4
    [53, "Wrong operand types"],                        # 5
    [54, "Accessing variable that does not exist."],    # 6
    [55, "This frame does not exist."],                 # 7
    [56, "Missing value"],                              # 8
    [57, "Wrong operand values."],                      # 9
    [58, "Invalid string operation"],                   # 10
]

varTypes = [
    'bool',
    'int',
    'string'
]


def call_error(line, error):
    """
    :param error: Error position in array
    :param line: number of line in code (debug purpose)
    :return: Error value
    """
    if debug:
        print(f'{line}: {error[1]}')
    else:
        print(error[1], file=sys.stderr)
    exit(error[0])


def check_arguments():
    """
    Function checks program arguments
    :return: Success or error value
    """
    if len(sys.argv) == 2:

        if sys.argv[1] == '--help':
            print('''This is an interpreter for IPPcode19. List of possible arguments: 
--help           Prints this help message and exits the program
                 
--source=file    Set the XML source file for the interpreter
--input=file     Set the input for interpreter
!Note: If either of these is missing, standard input is used.
 However, at least one of them has to be present, otherwise an error is thrown.
                   ''')
            exit(0)

        try:
            sourcefile, inputfile = "", ""
            options, arguments = getopt.getopt(sys.argv[1:], "", ["source=", "input="])

            for option, argument in options:
                # Load filenames from the arguments
                if option == '--source':
                    sourcefile = argument
                    if sourcefile == '':
                        raise ValueError
                elif option == '--input':
                    inputfile = argument
                    if inputfile == '':
                        raise ValueError
                else:
                    call_error("109", errorType[0])  # return 10
            return sourcefile, inputfile

        except getopt.GetoptError:
            call_error("113", errorType[0])  # Invalid arguments, return 10
        except ValueError:
            call_error("115", errorType[0])  # Argument has been used, but file not specified, return 10

    elif len(sys.argv) == 3:
        sourcefile, inputfile = "", ""
        try:
            options, arguments = getopt.getopt(sys.argv[1:], "", ["source=", "input="])
            for option, argument in options:
                # Load filenames from the arguments
                if option == '--source':
                    sourcefile = argument
                    if sourcefile == '':
                        raise ValueError
                elif option == '--input':
                    inputfile = argument
                    if inputfile == '':
                        raise ValueError
                else:
                    call_error("132", errorType[0])  # Return 10

            return sourcefile, inputfile

        except getopt.GetoptError:
            call_error("137", errorType[0])  # Invalid arguments, return 10

        except ValueError:
            call_error("140", errorType[0])  # Argument has been used, but file not specified, return 10

    else:
        call_error("143", errorType[0])  # Invalid arguments, return 10


def get_root(xml):
    """
    Function receives XML code, returns XML root
    :param xml: IPPcode19 in XML representation
    :return: XML root
    """
    try:
        root = xmlTree.parse(xml).getroot()
        return root
    except xmlTree.ParseError:
        call_error("156", errorType[2])  # Invalid XML format, return 31


def check_header(root):
    """
    Check if the code has a correct header
    :param root: Root of XML representation of IPPcode19
    :return: true if its correct
    """
    if (root.tag != "program") or ('language' not in root.attrib) or (root.attrib['language'] != "IPPcode19"):
        return False
    return True


def is_in(name, _list):
    """
    Check if an item is in an nested array
    :param name: name of instruction
    :param _list: list of nested instructions
    :return: validity of instruction
    """
    for element in _list:
        if element[0] == name:
            return True
    return False


def return_instruction_info(name, _list):
    """
    Returns instruction name and count of arguments
    :param name: name of instruction to look for
    :param _list: list of instructions with arg count
    :return: list entry for given instruction
    """
    i = 0
    for element in _list:
        if element[0] == name.upper():
            return _list[i]
        i += 1


def check_instruction_validity(root):
    """
    Checks if all the instructions are correct and if they have correct order
    :param root: Root of XML representation of IPPcode19
    :return: true if there are no errors
    """
    order = 1
    try:
        for line in root:
            if is_in(line.attrib["opcode"].upper(), instructions):
                if int(line.attrib["order"]) == order:
                    if debug:
                        current = line.attrib["opcode"]
                        print(f"{order} {current}")
                    order += 1
                else:
                    raise ValueError
            else:
                raise ValueError
    except ValueError:
        call_error("190", errorType[3])  # Return 32


def check_argument_count(arguments):
    """
    Checks if the count of arguments is valid for a given instruction
    :param arguments: xml data with arguments
    :return: True
    """
    arg_count = len(arguments)
    info = return_instruction_info(arguments.attrib['opcode'], instructions)
    if arg_count != info[1]:
        call_error("196", errorType[7])  # Return 55
    return True


def check_argument_type(argument, _type):
    """
    Check if the argument type is correct
    :param argument: argument
    :param _type: correct type
    :return: validity of type
    """
    if argument.attrib["type"] != _type:
        return False
    return True


def handle_values(_type, value, _labels):
    """
    Handles values from instruction argument so that they can be written into value_list
    :param _type: type of argument
    :param value: current argument
    :param _labels: list of labels to write to
    :return: value or argument
    """
    ret = []
    if _type == 'int':
        try:
            int(value.text)
            if not check_argument_type(value, value.attrib["type"]):
                raise ValueError
        except ValueError:
            call_error("213", errorType[5])
        ret.append(value.text)
        ret.append(value.attrib["type"])
        return ret

    if _type == 'var':
        if not (
                "GF@" in value.text
                or "TF@" in value.text
                or "LF@" in value.text):
            call_error("222", errorType[5])  # Wrong operand types, return 53
        ret.append(value.text)
        ret.append(value.attrib["type"])
        return ret

    if _type == 'bool':
        if not (value.text == 'true'
                or value.text == 'false'):
            call_error("228", errorType[9])  # Wrong operand values, return 57
        if not check_argument_type(value, value.attrib["type"]):
            call_error("230", errorType[5])  # Wrong operand types, return 53
        ret.append(value.text)
        ret.append(value.attrib["type"])
        return ret

    if _type == 'string':
        temp = value.text
        if temp is None:
            ret.append("")
            ret.append(value.attrib["type"])
            return ret
        if isinstance(temp, str):
            ret.append(temp)
            ret.append(value.attrib["type"])
            return ret
        # TODO handle escape sequences

        call_error("241", errorType[5])  # Wrong operand types, return 53

    if _type == 'label':
        if value.text not in _labels:
            call_error("245", errorType[4])  # Label does not exist, return 54 TODO
        ret.append(value.text)
        ret.append(value.attrib["type"])
        return ret

    if _type == 'type':
        if value.text not in varTypes:
            call_error("250", errorType[9])  # Wrong operand values, return 57

    if _type == "nil":
        ret.append(value.text)
        ret.append(value.attrib["type"])
        return ret

    call_error("252", errorType[5])


def validate_string(value):
    """
    Checks if supposed string is really a string
    :param value: variable to be checked
    :return: true or error
    """
    if isinstance(value, str):
        return True
    call_error("258", errorType[5])  # Wrong operand types


def validate_int(value):
    """
    Checks if supposed int is really a int
    :param value: variable to be checked
    :return: true or error
    """
    try:
        int(value)
    except (ValueError,TypeError):
        call_error("268", errorType[5])  # 53?


def validate_bool(value):
    """
    Checks if supposed bool is really a bool
    :param value: variable to be checked
    :return: true or error
    """
    if value in ['true', 'false']:
        return True
    call_error("273", errorType[5])  # 53?


# Operations ###########################################################


def _defvar(var, _global, local, temp):
    """
    Defines a variable in a given frame
    :param var: variable to be defined
    :param _global: global frame
    :param local: local frame
    :param temp: temp frame
    :return: void
    """
    if debug:
        print(f'_DEFVAR: {var}')
    frame_name = var[:2]
    var_name = var[3:]

    if frame_name == "TF":
        if temp_frame is None:
            call_error("273", errorType[7])  # Frame does not exist, return 55
        temp[var_name] = None

    elif frame_name == "LF":
        if local_frame is None:
            call_error("278", errorType[7])  # Frame does not exist, return 55
        local[var_name] = None

    elif frame_name == "GF":
        _global[var_name] = None

    else:
        call_error("285", errorType[7])  # Frame does not exist, return 55 TODO


def check_if_label_exists(var, _global, local, temp):
    """
    Function checks if label exists in corresponding frame
    :param var: variable containing frame_name and var_name
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :return: void
    """

    frame_name = var[:2]
    var_name = var[3:]

    if debug:
        print(frame_name)

    if frame_name == "TF":
        if temp is None:
            call_error("303", errorType[7])
        else:
            if var_name not in temp:
                call_error("306", errorType[6])

    elif frame_name == "LF":
        if local is None:
            call_error("310", errorType[7])
        else:
            if var_name not in local:
                call_error("313", errorType[6])

    elif frame_name == "GF":
        if var_name not in _global:
            call_error("317", errorType[6])

    else:
        call_error("320", errorType[7])  # TODO Frame does not exist or something else


def check_type(_type):
    """
    Function checks if the type is correct
    :param _type: type to be compared
    :return: error value if incorrect
    """
    if (_type == "var" or _type == "int"
        or _type == "bool" or _type == "label"
            or _type == "string" or _type == "type" or _type == "nil"):
        return
    call_error("333", errorType[5])


def get_type(var):
    """
    Returns type of argument
    :param var: argument
    :return: type
    """
    temp = var
    if isinstance(temp, int):
        return "int"
    elif isinstance(temp, str) and temp not in ["false", "true"]:
        if temp is None or temp:
            return "string"
    elif isinstance(temp, str):
        if temp in ["false", "true"]:
            return "bool"


def retrieve_value(var, _global, local, temp):
    """
    Retrieves value from a variable
    :param var: variable name
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :return:
    """
    var_name = var[3:]
    if var_name in _global:
        return _global[var_name]

    elif local is not None:
        if var_name in local:
            return local[var_name]

    elif temp is not None:
        if var_name in temp:
            return temp[var_name]

    else:
        call_error("362", errorType[6])  # Variable does not exist, return 54


def retrieve_value_bool(var, value_list, _global, local, temp, _labels):
    """
    Retrieves bool value from a variable
    :param var: variable name
    :param value_list: list of values of the program
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :param _labels: list of labels
    :return: values in the value list
    """
    curr_value = handle_values('var', var[0], _labels)
    value_list.append(curr_value)
    check_if_label_exists(value_list[0][0], _global, local, temp)
    _type_first = var[1].attrib["type"]
    check_type(_type_first)
    _type_second = var[2].attrib["type"]
    check_type(_type_second)
    if _type_first == "bool" or _type_first == "var":
        curr_value = handle_values(_type_first, var[1], _labels)
        value_list.append(curr_value)
        if _type_first == "var":
            check_if_label_exists(value_list[1][0], _global, local, temp)
            value_list[1][0] = retrieve_value(value_list[1][0], _global, local, temp)
    else:
        call_error("402", errorType[9])

    if _type_second == "bool" or _type_second == "var":
        curr_value = handle_values(_type_second, var[2], _labels)
        value_list.append(curr_value)
        if _type_second == "var":
            check_if_label_exists(value_list[2][0], _global, local, temp)
            value_list[2][0] = retrieve_value(value_list[2][0], _global, local, temp)
    else:
        call_error("402", errorType[9])

    validate_bool(value_list[1][0])
    validate_bool(value_list[2][0])


def retrieve_multiple_values_aritmetical(var, value_list, _global, local, temp, _labels):
    """
    Retrieves multiple values for an aritmetical operation
    :param var: variable name
    :param value_list: list of values of the program
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :param _labels: list of labels
    :return: values in the value list
    """
    curr_value = handle_values('var', var[0], _labels)
    value_list.append(curr_value)
    var_name = var[0].text
    check_if_label_exists(var_name, _global, local, temp)
    _type_first = var[1].attrib['type']
    check_type(_type_first)
    _type_second = var[2].attrib['type']
    check_type(_type_second)

    if _type_first == "int":
        curr_value = handle_values(_type_first, var[1], _labels)
        value_list.append(curr_value)

    elif _type_first == "var":
        curr_value = handle_values(_type_first, var[1], _labels)
        value_list.append(curr_value)
        check_if_label_exists(value_list[1][0], _global, local, temp)
        value_list[1][0] = retrieve_value(value_list[1][0], _global, local, temp)
        validate_int(value_list[1][0])

    else:
        call_error("420", errorType[9])

    if _type_second == "int":
        curr_value = handle_values(_type_second, var[2], _labels)
        value_list.append(curr_value)

    elif _type_second == "var":
        curr_value = handle_values(_type_second, var[2], _labels)
        value_list.append(curr_value)
        check_if_label_exists(value_list[2][0], _global, local, temp)
        value_list[2][0] = retrieve_value(value_list[2][0], _global, local, temp)
        validate_int(value_list[2][0])

    else:
        call_error("420", errorType[9])


def retrieve_multiple_values_relational(var, value_list, _global, local, temp, _labels):
    """
    Retrieves multiple values for an relational operation
    :param var: variable name
    :param value_list: list of values of the program
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :param _labels: list of labels
    :return: values in the value list
    """
    curr_value = handle_values("var", var[0], _labels)
    value_list.append(curr_value)
    check_if_label_exists(value_list[0][0], _global, local, temp)
    _type_first = var[1].attrib["type"]
    check_type(_type_first)
    _type_second = var[2].attrib["type"]
    check_type(_type_second)
    if _type_first == "int" or _type_first == "var" or _type_first == "bool" or _type_first == "string":
        curr_value = handle_values(_type_first, var[1], labels)
        value_list.append(curr_value)
        if _type_first == "var":
            check_if_label_exists(value_list[1][0], _global, local, temp)
            value_list[1][0] = retrieve_value(value_list[1][0], _global, local, temp)
            value_list[1][1] = get_type(value_list[1][0])
    else:
        call_error("441", errorType[9])

    if _type_second == "int" or _type_second == "var" or _type_second == "bool" or _type_second == "string":
        curr_value = handle_values(_type_second, var[2], labels)
        value_list.append(curr_value)
        if _type_second == "var":
            check_if_label_exists(value_list[2][0], _global, local, temp)
            value_list[2][0] = retrieve_value(value_list[2][0], _global, local, temp)
            value_list[2][1] = get_type(value_list[2][0])
    else:
        call_error("450", errorType[9])
    _type_first = get_type(value_list[1][0])
    if debug:
        print(_type_first)
    value_list.append(_type_first)


def set_value(var, val, _global, local, temp):
    """
    Set a value of a variable in a given frame
    :param var: variable name
    :param val: value to be set
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    """
    var_name = var[3:]
    frame_name = var[:2]
    if debug:
        print("Set value:", var_name, frame_name)
    if frame_name == "GF":
        _global[var_name] = val

    elif frame_name == "LF":
        if local is None:
            call_error("374", errorType[7])
        local[var_name] = val

    elif frame_name == "TF":
        if temp is None:
            call_error("379", errorType[7])
        temp[var_name] = val


def var_dump(frame, _global, local, temp, _labels):
    """
    Listing variables, used for debugging purposes
    :param frame: frame to list
    :param _global: Global Frame
    :param local: Local Frame
    :param temp: Temporary Frame
    :param _labels: Labels list
    """
    if frame == "global":
        for item in _global:
            print(item)

    elif frame == "local":
        if local is None:
            call_error("390", errorType[7])
        for item in local:
            print(item)

    elif frame == "temp":
        if temp is None:
            call_error("396", errorType[7])
        for item in temp:
            print(item)

    elif frame == "labels":
        for item in _labels:
            print(item)


def translate_escapes(_str):
    """
    Changes escape sequences inside strings
    :param _str: string
    :return: translated string
    """
    regex_groups = re.findall(r"\\\d(\d{2})", _str)
    for item in regex_groups:
        temp = "\\0"+str(item)
        temp_char = chr(int(item))
        _str = _str.replace(str(temp), temp_char)
    return _str


def interpreter(root, input_data):
    """
    Interprets correct XML Input code
    :param root: Root of XML representation of IPPcode19
    :return: TODO
    """

    global global_frame, local_frame, temp_frame, frame_stack, labels, stack, function_stack
    instruction_count = 0
    for line in root:
        instruction_count += 1
        value_list = []
        if debug:
            print(f'line.attrib: {line.attrib}')
            for child in line:
                print(f'child.text: {child.text}')

        current_op = line.attrib['opcode'].upper()
        check_argument_count(line)

        # These are done
        if current_op == 'CREATEFRAME':
            temp_frame = {}

        elif current_op == 'PUSHFRAME':
            if temp_frame is None:
                call_error("427", errorType[7])  # Frame does not exist, return 55
            
            frame_stack.append(temp_frame)
            temp_frame = None
            local_frame = frame_stack[len(frame_stack)-1]

        elif current_op == 'POPFRAME':
            if frame_stack is None:
                call_error("435", errorType[7])  # Frame does not exist, return 55
            if not frame_stack:
                call_error("569", errorType[7])

            temp_frame = frame_stack[0]
            frame_stack.pop(0)
            if len(frame_stack) >= 1:
                local_frame = frame_stack[len(frame_stack)-1]
            else:
                local_frame = None

        elif current_op == 'DEFVAR':
            curr_value = handle_values("var", line[0], labels)
            if debug:
                print(f'DEFVAR: {curr_value}')
            value_list.append(curr_value)
            _defvar(value_list[len(value_list)-1][0], global_frame, local_frame, temp_frame)
            # var_dump("global", global_frame, local_frame, temp_frame, labels)

        # These are To Be Done TODO
        elif current_op == 'MOVE':
            _type = line[1].attrib["type"]
            check_type(_type)
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            curr_value = handle_values(_type, line[1], labels)
            value_list.append(curr_value)

            if _type == "var":
                check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                _type = get_type(value_list[1][0])

            if _type == "string":
                if value_list[1][0] == "":
                    value_list[1][0] = ""
                    value_list[1][1] = "string"
                else:
                    validate_string(value_list[1][0])
                value_list[1][0] = translate_escapes(value_list[1][0])
                value_list[1][1] = "string"

            elif _type == "int":
                value_list[1][0] = int(value_list[1][0])

            elif _type == "bool":
                value_list[1][0] = value_list[1][0]

            if value_list[1][0] is None:
                call_error("471", errorType[8])
            set_value(value_list[0][0], value_list[1][0], global_frame, local_frame, temp_frame)

        elif current_op == 'READ':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            # TODO exceptions
            _input = ''

            temp = ''
            try:
                if len(input_data) == 0:
                    #_input = input()
                    _input = "test"
                else:
                    _input = input_data.pop(0)
            except:
                _type = line[1].attrib["type"]
                check_type(_type)
                if _type == "type":
                    _type_temp = line[1].text
                    if _type_temp == "int":
                        temp = 0
                    elif _type_temp == "string":
                        temp = ''
                    elif _type_temp == "bool":
                        temp = False
                    else:
                        call_error("626", errorType[9])
            else:
                _type = line[1].attrib["type"]
                check_type(_type)
                if _type == "type":
                    _type_temp = line[1].text
                    if _type_temp == "int":
                        try:
                            int(_input)
                        except:
                            temp = 0
                        else:
                            temp = _input
                    elif _type_temp == "string":
                        temp = translate_escapes(_input)
                    elif _type_temp == "bool":
                        if _input.lower() == "true":
                            temp = "bool@true"
                        else:
                            temp = "bool@false"
                    else:
                        call_error("661", errorType[3])
            set_value(value_list[0][0], temp, global_frame, local_frame, temp_frame)

        elif current_op == 'WRITE':
            _type = line[0].attrib["type"]
            check_type(_type)
            curr_value = handle_values(_type, line[0], labels)
            value_list.append(curr_value)
            if _type == "var":
                check_if_label_exists(line[0].text, global_frame, local_frame, temp_frame)
                value_list[0][0] = retrieve_value(value_list[0][0], global_frame, local_frame, temp_frame)
                _type = get_type(value_list[0][0])
            elif _type == "int":
                validate_int(value_list[0][0])
            elif _type == "string":
                validate_string(value_list[0][0])
            elif _type == "bool":
                validate_bool(value_list[0][0].lower())
            value_list[0][0] = str(value_list[0][0])
            if value_list[0][0] == "None":
                call_error("525", errorType[8])
            print(translate_escapes(value_list[0][0]), end='')

        elif current_op == 'BREAK':
            print("Current state of interpretation.. ")
            print(f"Total instructions:     {len(root)}")
            print(f"Finished instructions:  {instruction_count-1}")
            print(f"Current instruction:    {instruction_count}")
            print(f"Global Frame:           {global_frame}")
            print(f"Local Frame:            {local_frame}")
            print(f"Temporary Frame:        {temp_frame}")

        elif current_op == 'EXIT':
            exit_retval = -1
            _type = line[0].attrib["type"]
            check_type(_type)
            if _type != "int" and _type != "var":
                call_error("705", errorType[5])
            if _type == "var":
                curr_value = handle_values("var", line[0], labels)
                value_list.append(curr_value)
                check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
                exit_retval = retrieve_value(value_list[0][0], global_frame, local_frame, temp_frame)
            else:
                exit_retval = line[0].text
            try:
                if exit_retval is None:
                    call_error("696", errorType[8])
                exit_retval = int(exit_retval)
            except ValueError:
                call_error("549", errorType[8])
            if 0 <= exit_retval < 50:
                sys.exit(exit_retval)
            call_error("552", errorType[9])

        elif current_op == 'DPRINT':
            _type = line[0].attrib["type"]
            check_type(_type)
            if _type == "var":
                curr_value = handle_values(_type, line[0], labels)
                value_list.append(curr_value)
                check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
                value_list[0][0] = retrieve_value(value_list[0][0], global_frame, local_frame, temp_frame)
            elif _type == "string":
                value_list.append(line[0].text)
            try:
                value_list[0][0] = str(value_list[0][0])
            except ValueError:
                call_error("568", errorType[9])  # TODO
            print(value_list[0][0], file=sys.stderr)

        elif current_op == 'ADD':
            retrieve_multiple_values_aritmetical(line, value_list, global_frame, local_frame, temp_frame, labels)
            try:
                value_list[1][0] = int(value_list[1][0])
                value_list[2][0] = int(value_list[2][0])
            except ValueError:
                call_error("620", errorType[9])
            aritmetic_res = value_list[1][0] + value_list[2][0]
            set_value(value_list[0][0], aritmetic_res, global_frame, local_frame, temp_frame)

        elif current_op == 'SUB':
            retrieve_multiple_values_aritmetical(line, value_list, global_frame, local_frame, temp_frame, labels)
            try:
                value_list[1][0] = int(value_list[1][0])
                value_list[2][0] = int(value_list[2][0])
            except ValueError:
                call_error("620", errorType[9])
            aritmetic_res = value_list[1][0] - value_list[2][0]
            set_value(value_list[0][0], aritmetic_res, global_frame, local_frame, temp_frame)

        elif current_op == 'MUL':
            retrieve_multiple_values_aritmetical(line, value_list, global_frame, local_frame, temp_frame, labels)
            try:
                value_list[1][0] = int(value_list[1][0])
                value_list[2][0] = int(value_list[2][0])
            except ValueError:
                call_error("620", errorType[9])
            aritmetic_res = value_list[1][0] * value_list[2][0]
            set_value(value_list[0][0], aritmetic_res, global_frame, local_frame, temp_frame)

        elif current_op == 'IDIV':
            retrieve_multiple_values_aritmetical(line, value_list, global_frame, local_frame, temp_frame, labels)
            try:
                value_list[1][0] = int(value_list[1][0])
                value_list[2][0] = int(value_list[2][0])
                if value_list[2][0] == 0:
                    raise ValueError
            except ValueError:
                call_error("620", errorType[9])
            aritmetic_res = value_list[1][0] // value_list[2][0]
            set_value(value_list[0][0], aritmetic_res, global_frame, local_frame, temp_frame)

        elif current_op == 'STRLEN':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type = line[1].attrib["type"]
            check_type(_type)
            if _type == "var":
                curr_value = handle_values("var", line[1], labels)
                value_list.append(curr_value)
                check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                _type = get_type(value_list[1])
                if value_list[1][0] is None:
                    call_error("797", errorType[8])
                validate_string(value_list[1][0])
            elif _type == "string":
                curr_value = handle_values("string", line[1], labels)
                value_list.append(curr_value)
                validate_string(value_list[1][0])
            else:
                call_error("678", errorType[9])
            set_value(value_list[0][0], len(value_list[1][0]), global_frame, local_frame, temp_frame)

        elif current_op == 'LT':
            retrieve_multiple_values_relational(line, value_list, global_frame, local_frame, temp_frame, labels)
            if value_list[3][0] == "int":
                try:
                    value_list[1][0] = int(value_list[1][0])
                    value_list[2][0] = int(value_list[2][0])
                except ValueError:
                    call_error("716", errorType[9])
            elif value_list[3][0] == "bool":
                value_list[1][0] = (value_list[1][0])
                value_list[2][0] = (value_list[2][0])
            elif value_list[3][0] == "string":
                value_list[1][0] = str(value_list[1][0])
                value_list[2][0] = str(value_list[2][0])
            else:
                call_error("818", errorType[5])
            if value_list[3][0] == "string":
                if value_list[1][0] == "false" and value_list[2][0] == "true":
                    set_value(value_list[0][0], "true", global_frame, local_frame,temp_frame)
                elif value_list[1][0] == "true" and value_list[2][0] == "false":
                    set_value(value_list[0][0], "false", global_frame, local_frame, temp_frame)
                set_value(value_list[0][0], str(value_list[1][0] < value_list[2][0]).lower(), global_frame, local_frame,
                          temp_frame)
            else:
                set_value(value_list[0][0], str(value_list[1][0] < value_list[2][0]).lower(), global_frame, local_frame,
                          temp_frame)

        elif current_op == 'GT':
            retrieve_multiple_values_relational(line, value_list, global_frame, local_frame, temp_frame, labels)
            if debug:
                print(value_list[3][0])
            if value_list[3] == "int":
                try:
                    value_list[1][0] = int(value_list[1][0])
                    value_list[2][0] = int(value_list[2][0])
                except ValueError:
                    call_error("716", errorType[9])
            elif value_list[3] == "bool":
                value_list[1][0] = str(value_list[1][0])
                value_list[2][0] = str(value_list[2][0])
            elif value_list[3] == "string":
                value_list[1][0] = str(value_list[1][0])
                value_list[2][0] = str(value_list[2][0])
            else:
                call_error("846", errorType[5])
            if value_list[3][0] == "string":
                if value_list[1][0] == "false" and value_list[2][0] == "true":
                    set_value(value_list[0][0], "false", global_frame, local_frame,temp_frame)
                elif value_list[1][0] == "true" and value_list[2][0] == "false":
                    set_value(value_list[0][0], "true", global_frame, local_frame, temp_frame)
                set_value(value_list[0][0], str(value_list[1][0] > value_list[2][0]).lower(), global_frame, local_frame,
                          temp_frame)
            else:
                set_value(value_list[0][0], str(value_list[1][0] > value_list[2][0]).lower(), global_frame, local_frame,
                          temp_frame)

        elif current_op == 'EQ':
            retrieve_multiple_values_relational(line, value_list, global_frame, local_frame, temp_frame, labels)
            if debug:
                print(value_list)
            if value_list[3][0] == "int":
                try:
                    value_list[1][0] = int(value_list[1][0])
                    value_list[2][0] = int(value_list[2][0])
                except (ValueError, TypeError):
                    call_error("716", errorType[9])
            elif value_list[3][0] == "bool":
                value_list[1][0] = (value_list[1][0])
                value_list[2][0] = (value_list[2][0])
            elif value_list[3][0] == "string":
                value_list[1][0] = str(value_list[1][0])
                value_list[2][0] = str(value_list[2][0])
            set_value(value_list[0][0], str(value_list[1][0] == value_list[2][0]).lower(), global_frame, local_frame,
                      temp_frame)

        elif current_op == 'PUSHS':
            _type = line[0].attrib["type"]
            check_type(_type)
            curr_value = handle_values(_type, line[0], labels)
            value_list.append(curr_value)
            if _type == "var":
                check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
                value_list[0][0] = retrieve_value(value_list[0][0], global_frame, local_frame, temp_frame)
            elif _type == "type" or _type == "label":
                call_error("792", errorType[9])
            stack.append(value_list[0][0])

        elif current_op == 'POPS':
            if len(stack) > 0:
                _type = line[0].attrib["type"]
                check_type(_type)
                if _type == "var":
                    curr_value = handle_values(_type, line[0], labels)
                    value_list.append(curr_value)
                    check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
                else:
                    call_error("806", errorType[8])

                set_value(value_list[0][0], stack[len(stack)-1], global_frame, local_frame, temp_frame)
            else:
                call_error("907", errorType[8])

        elif current_op == 'CALL':
            curr_value = handle_values("label", line[0], labels)
            value_list.append(curr_value)
            if value_list[0][0] not in labels:
                call_error("818", errorType[4])
            function_stack.append(int(line.attrib["order"]))
            temp = root[labels[line[0].text]:]
            interpreter(temp, None)
            break

        elif current_op == 'RETURN':
            if not function_stack == []:
                interpreter(root[function_stack.pop():], None)
            else:
                call_error("1063", errorType[8])
            break

        elif current_op == 'JUMP':
            var_dump("labels", global_frame, local_frame, temp_frame, labels)
            
            curr_value = handle_values("label", line[0], labels)
            value_list.append(curr_value)
            if value_list[0][0] in labels:
                interpreter(root[(labels[value_list[0][0]]):], None)
            else:
                call_error("1073", errorType[4])
            break

        elif current_op == 'JUMPIFEQ':
            continue

        elif current_op == "JUMPIFNEQ":
            continue

        elif current_op == 'AND':
            retrieve_value_bool(line, value_list, global_frame, local_frame, temp_frame, labels)
            if value_list[1][0] == 'true' and value_list[2][0] == 'true':
                set_value(value_list[0][0], 'true', global_frame, local_frame, temp_frame)
            else:
                set_value(value_list[0][0], 'false', global_frame, local_frame, temp_frame)

        elif current_op == 'OR':
            retrieve_value_bool(line, value_list, global_frame, local_frame, temp_frame, labels)
            if value_list[1][0] == 'true' or value_list[2][0] == 'true':
                set_value(value_list[0][0], 'true', global_frame, local_frame, temp_frame)
            else:
                set_value(value_list[0][0], 'false', global_frame, local_frame, temp_frame)

        elif current_op == 'NOT':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type = line[1].attrib["type"]
            if _type == "bool":
                curr_value = handle_values("bool", line[1], labels)
            elif _type == "var":
                curr_value = handle_values("var", line[1], labels)
                value_list.append(curr_value)
                check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
            value_list.append(curr_value)
            if value_list[1][0] == 'false':
                set_value(value_list[0][0], 'true', global_frame, local_frame, temp_frame)
            else:
                set_value(value_list[0][0], 'false', global_frame, local_frame, temp_frame)

        elif current_op == 'GETCHAR':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type_first = line[1].attrib["type"]
            check_type(_type_first)
            _type_second = line[2].attrib["type"]
            check_type(_type_second)

            if _type_first == "string" or _type_first == "var":
                curr_value = handle_values(_type_first, line[1], labels)
                value_list.append(curr_value)
                if _type_first == "var":
                    check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)

                if value_list[1][0] is None:
                    call_error("922", errorType[8])
            else:
                call_error("897", errorType[9])
            if _type_second == "int" or _type_second == "var":
                curr_value = handle_values(_type_second, line[2], labels)
                value_list.append(curr_value)
                if value_list[2][0] is None:
                    call_error("930", errorType[8])
                if _type_second == "var":
                    check_if_label_exists(value_list[2][0], global_frame, local_frame, temp_frame)
                    value_list[2][0] = retrieve_value(value_list[2][0], global_frame, local_frame, temp_frame)

                try:
                    value_list[2][0] = int(value_list[2][0])
                except ValueError:
                    call_error("905", errorType[5])
                except TypeError:
                    call_error("972", errorType[8])

            else:
                call_error("910", errorType[9])

            validate_string(value_list[1][0])
            validate_int(value_list[2][0])

            if len(value_list[1][0]) <= value_list[2][0]:
                call_error("911", errorType[10])
            set_value(value_list[0][0], value_list[1][0][value_list[2][0]], global_frame, local_frame, temp_frame)

        elif current_op == 'SETCHAR':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type_first = line[1].attrib["type"]
            check_type(_type_first)
            _type_second = line[2].attrib["type"]
            check_type(_type_second)

            if _type_first == "int" or _type_first == "var":
                curr_value = handle_values(_type_first, line[1], labels)
                value_list.append(curr_value)
                if _type_first == "var":
                    check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                validate_int(value_list[1][0])
            else:
                call_error("897", errorType[9])
            if _type_second == "string" or _type_second == "var":
                curr_value = handle_values(_type_second, line[2], labels)
                value_list.append(curr_value)
                if _type_second == "var":
                    check_if_label_exists(value_list[2][0], global_frame, local_frame, temp_frame)
                    value_list[2][0] = retrieve_value(value_list[2][0], global_frame, local_frame, temp_frame)
            else:
                call_error("910", errorType[9])

            try:
                value_list[1][0] = int(value_list[1][0])
            except (ValueError, TypeError):
                call_error("940", errorType[9])
            validate_string(value_list[2][0])
            temp = str(retrieve_value(value_list[0][0], global_frame, local_frame, temp_frame))
            if len(temp) <= value_list[1][0] or len(value_list[2][0]) == 0:
                call_error("944", errorType[10])
            set_value(value_list[0][0], temp[:value_list[1][0]] + str(value_list[2][0]) + temp[value_list[1][0]+1:], global_frame, local_frame, temp_frame)

        elif current_op == 'CONCAT':
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type_first = line[1].attrib["type"]
            check_type(_type_first)
            _type_second = line[2].attrib["type"]
            check_type(_type_second)
            if _type_first == "string" or _type_first == "var":
                curr_value = handle_values(_type_first, line[1], labels)
                value_list.append(curr_value)
                if _type_first == "var":
                    check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][1] = get_type(value_list[1][0])
            else:
                call_error("963", errorType[9])

            if _type_second == "string" or _type_second == "var":
                curr_value = handle_values(_type_second, line[2], labels)
                value_list.append(curr_value)
                if _type_second == "var":
                    check_if_label_exists(value_list[2][0], global_frame, local_frame, temp_frame)
                    value_list[2][0] = retrieve_value(value_list[2][0], global_frame, local_frame, temp_frame)
                    if value_list[2][0] == "":
                        value_list[2][1] = "string"
                    else:
                        value_list[2][1] = get_type(value_list[2][0])

            else:
                call_error("963", errorType[9])
            if debug:
                print(value_list)
            if value_list[1][0] is None or value_list[2][0] is None:
               call_error("1233", errorType[8])

            if value_list[1][0] == "":
                value_list[1][1] = "string"
            if value_list[2][0] == "":
                value_list[2][1] = "string"
            if value_list[1][1] != "string" or value_list[2][1] != "string":
                call_error("1207", errorType[5])
            validate_string(value_list[1][0])
            validate_string(value_list[2][0])

            set_value(value_list[0][0], value_list[1][0]+value_list[2][0], global_frame, local_frame, temp_frame)

        elif current_op == "TYPE":
            # TODO if var does not exist, type is nil
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type = line[1].attrib["type"]
            check_type(_type)

            if _type != "var":
                set_value(value_list[0][0], _type, global_frame, local_frame, temp_frame)

            else:
                curr_value = handle_values(_type, line[1], labels)
                value_list.append(curr_value)
                check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                if type(value_list[1][0]) is int:
                    set_value(value_list[0][0], "int", global_frame, local_frame, temp_frame)
                elif type(value_list[1][0]) is str and value_list[1][0] in ['true', 'false']:
                    set_value(value_list[0][0], "bool", global_frame, local_frame, temp_frame)
                elif type(value_list[1][0]) is str and value_list[1][0] == "nil":
                    set_value(value_list[0][0], "nil", global_frame, local_frame, temp_frame)
                elif type(value_list[1][0]) is str:
                    set_value(value_list[0][0], "string", global_frame, local_frame, temp_frame)
                elif type(value_list[1][0]) is None:
                    set_value(value_list[0][0], "nil", global_frame, local_frame, temp_frame)

        elif current_op == "STRI2INT":
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type_first = line[1].attrib["type"]
            check_type(_type_first)
            _type_second = line[2].attrib["type"]
            check_type(_type_second)

            if _type_first == "string" or _type_first == "var":
                curr_value = handle_values(_type_first, line[1], labels)
                value_list.append(curr_value)
                if _type_first == "var":
                    check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
                    validate_string(value_list[1][0])
            else:
                call_error("1022", errorType[9])

            if _type_second == "int" or _type_second == "var":
                curr_value = handle_values(_type_second, line[2], labels)
                value_list.append(curr_value)
                if _type_second == "var":
                    check_if_label_exists(value_list[2][0], global_frame, local_frame, temp_frame)
                    value_list[2][0] = retrieve_value(value_list[2][0], global_frame, local_frame, temp_frame)
                if value_list[2][0] is None:
                    call_error("1100", errorType[8])
                try:
                    value_list[2][0] = int(value_list[2][0])
                except (ValueError,TypeError):
                    call_error("1118", errorType[5])
            else:
                call_error("1031", errorType[9])
            if len(value_list[1][0]) <= value_list[2][0]:
                call_error("1103", errorType[10])
            set_value(value_list[0][0], ord(value_list[1][0][value_list[2][0]]), global_frame, local_frame, temp_frame)

        elif current_op == "INT2CHAR":
            curr_value = handle_values("var", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            _type = line[1].attrib["type"]
            check_type(_type)

            if _type == "int" or _type == "var":
                curr_value = handle_values(_type, line[1], labels)
                value_list.append(curr_value)
                if _type == "var":
                    check_if_label_exists(value_list[1][0], global_frame, local_frame, temp_frame)
                    value_list[1][0] = retrieve_value(value_list[1][0], global_frame, local_frame, temp_frame)
            else:
                call_error("1053", errorType[9])

            try:
                value_list[1][0] = int(value_list[1][0])
                chr(value_list[1][0])
            except (ValueError, TypeError):
                call_error("1058", errorType[10])

            set_value(value_list[0][0], chr(value_list[1][0]), global_frame, local_frame, temp_frame)

        elif current_op == "LABEL":
            continue

        elif current_op == "JUMP":
            curr_value = handle_values("label", line[0], labels)
            value_list.append(curr_value)
            check_if_label_exists(value_list[0][0], global_frame, local_frame, temp_frame)
            interpreter(root[(labels[value_list[0][0]]):], input_data)


def find_jumps(root, _labels):
    """
    Runs through the code and finds all labels, saves them into labels
    :param root: root of xml representation of the code
    :param _labels: list of labels
    """
    for child in root:
        if child.attrib["opcode"] == "LABEL":
            if child.text in _labels:
                call_error("1187", errorType[4])
            check_argument_count(child)
            labels[child[0].text] = int(child.attrib["order"])


def main():
    """
    Main function of program
    Runs the interpreter
    :return: void
    """
    source_file, input_file = check_arguments()  # Retrieving filenames from arguments
    source_data = ""
    input_data = []

    if source_file == '':
        source_data = sys.stdin.readlines()  # If source is empty, read from stdin
    else:
        try:
            with open(source_file) as source:
                source_data = source.readlines()
        except IOError:
            call_error("504", errorType[1])  # input file error, return 11

    if input_file == '':
        input_data = sys.stdin.readlines()

    else:
        try:
            with open(input_file) as _input:
                input_data = _input.readlines()
        except IOError:
            call_error("513", errorType[1])  # Input file error, return 11

    if debug:
        print(source_file, input_file)
        print(source_data, input_data)

    with open(source_file) as file:
        xml_root = get_root(file)
        if not check_header(xml_root):
            exit(errorType[2])  # Incorrect XML, return 31
        check_instruction_validity(xml_root)

    find_jumps(xml_root, labels)

    interpreter(xml_root, input_data)


if __name__ == '__main__':
    temp_frame = None
    local_frame = None
    global_frame = {}
    frame_stack = []
    function_stack = []
    stack = []
    labels = {}
    main()
