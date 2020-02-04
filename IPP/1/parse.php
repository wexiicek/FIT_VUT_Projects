<?php
define('DEBUG', 0);
define('SUCCESS', 0);

#Defining possible values
$valueTypes = array('bool', 'int', 'string');

# Open stdin to load the code
$input = fopen('php://stdin', 'r');

# Possible error messages
$errType = array (
    'params' => array (10, "Missing --stats= flag.\n"),
    'input' => array (11, "Input file cannot be opened"),
    'output' => array (12, "Output file cannot be opened."),
    'header' => array (21, "Missing or incorrect header.\n"),
    'opcode' => array (22, "Unknown or incorrect opcode.\n"),
    'other' => array (23, "Other lexical or syntactical error.\n"),
    'internal' => array (99, "Internal error.\n"),
);

$help = 'This script reads IPPcode19 from STDIN and returns XML to STDOUT.
You can use following arguments: --help, --stats=file, --loc, --comments, --labels, --jumps.
============================================================================================
--help					Display this help page

--stats=file    		Set output file for detailed statistics
    !Must be ran with at least one of the following args:
        --loc           Lines Of Code
        --comments      Lines with comments
        --labels        Label count
        --jumps         Jump count
';

# Class which represents an error.
class err {
    function call($type){
        fprintf(STDERR,$type[0]." ".$type[1]);
        exit($type[0]);
    }
}

# Class which represents a single instruction
class instruction {
    public $correctNumberOfArguments;
    public $argArr = array();
    public $instr = array();

    public function argCount ($count) {
        $this->correctNumberOfArguments = $count;
        return $this->correctNumberOfArguments;
    }

    public function getInstruction ($instr) {
        $this->instr = $instr;
    }
}

# List of instructions and its arguments
class instructionList extends instruction {
    public $list = array(
        'MOVE' => array(2, 'var', 'sym'),
        'CREATEFRAME' => array(0),
        'PUSHFRAME' => array(0),
        'POPFRAME' => array(0),
        'DEFVAR' => array(1, 'var'),
        'CALL' => array(1, 'lab'),
        'RETURN' => array(0),
        'PUSHS' => array(1, 'sym'),
        'POPS' => array(1, 'var'),
        'ADD' => array(3, 'var', 'sym', 'sym'),
        'SUB' => array(3, 'var', 'sym', 'sym'),
        'MUL' => array(3, 'var', 'sym', 'sym'),
        'IDIV' => array(3, 'var', 'sym', 'sym'),
        'LT' => array(3, 'var', 'sym', 'sym'),
        'GT' => array(3, 'var', 'sym', 'sym'),
        'EQ' => array(3, 'var', 'sym', 'sym'),
        'AND' => array(3, 'var', 'sym', 'sym'),
        'OR' => array(3, 'var', 'sym', 'sym'),
        'NOT' => array(2, 'var', 'sym'),
        'INT2CHAR' => array(2, 'var', 'sym'),
        'STRI2INT' => array(3, 'var', 'sym', 'sym'),
        'READ' => array(2, 'var', 'type'),
        'WRITE' => array(1, 'sym'),
        'CONCAT' => array(3, 'var', 'sym', 'sym'),
        'STRLEN' => array(2, 'var', 'sym'),
        'GETCHAR' => array(3, 'var', 'sym', 'sym'),
        'SETCHAR' => array(3, 'var', 'sym', 'sym'),
        'TYPE' => array(2, 'var', 'sym'),
        'LABEL' => array(1, 'lab'),
        'JUMP' => array(1, 'lab'),
        'JUMPIFEQ' => array(3, 'lab', 'sym', 'sym'),
        'JUMPIFNEQ' => array(3, 'lab', 'sym', 'sym'),
        'EXIT' => array(1, 'sym'),
        'DPRINT' => array(1, 'sym'),
        'BREAK' => array(0),
    );
}

class statistics {

    #Variables that keep track of various statistics
    public $numberOfComments = 0;
    public $numberOfLabels = 0;
    public $numberOfJumps = 0;
    public $numberOfLines = 0;

    public function returnLines(){
        return $this->numberOfLines-1;
    }

    #Variables that define regular expressions
    private	$isComment = '/#.*$/';
    private $isLabel = '/(LABEL)\s+.*/';
    private $isJump = '/(JUMPIFEQ|JUMPIFNEQ|JUMP)/';

    # Checks line of code for comments, which is then removed further
    public function checkForComments ($lineOfCode) {
        if (preg_match($this->isComment, $lineOfCode)) {
            $this->numberOfComments++;
            return true;
        }
        else {
            return false;
        }
    } #End of checkForComments

    #Checks if the operation contains a jump
    function checkForJump ($lineOfCode) {
        if (preg_match($this->isJump, $lineOfCode)) {
            $this->numberOfJumps++;
        }
    } #End of CheckForJump

    # Checks if the operation contains a label
    function checkForLabels ($lineOfCode) {
        if (preg_match($this->isLabel, $lineOfCode)) {
            $this->numberOfLabels++;
        }
    } #End of CheckForLabels

} #End of $stats class

# Check if the arguments are in the argument list
function validArguments($arg, $list){
    return array_key_exists($arg, $list);
}

# Convert string into lowercase
function toLower($str) {
    return mb_strtolower($str, "UTF-8");
}

# Convert string into uppercase
function toUpper($str) {
    return mb_strtoupper($str, "UTF-8");
}

# Check if an item is in array
function isInArray($str, $arr){
    if (array_key_exists($str, $arr)){
        return true;
    }
    return false;
}

# Returns an empty array
function emptyArray(){
    return array();
}

# Changes the characters into a representable XML form
function translateCharacters ($lineOfCode) {
    if (strpos($lineOfCode, '&') != false || substr($lineOfCode, 0, 1) == "&"){
        $lineOfCode = str_replace('&', '&amp;', $lineOfCode);
    }
    if (strpos($lineOfCode, '<') != false || substr($lineOfCode, 0, 1) == "<"){
        $lineOfCode = str_replace('<', '&lt;', $lineOfCode);
    }
    if (strpos($lineOfCode, '>') != false || substr($lineOfCode, 0, 1) == ">"){
        $lineOfCode = str_replace('>', '&gt;', $lineOfCode);
    }
    if ((strpos($lineOfCode, '\"') != false || substr($lineOfCode, 0, 1) == "\"") ||
        (strpos($lineOfCode, '\'') != false || substr($lineOfCode, 0, 1) == "\'")  ) {
        $lineOfCode = str_replace('\"', '&quot;', $lineOfCode);
        $lineOfCode = str_replace('\'', '&quot;', $lineOfCode);
    }
    return $lineOfCode;
}


# Check if the arguments are of a correct type
# and insert them into an array.
function validateParameters ($tokens) {
    # Initialize regex into variables
    $isVar = '/^(LF|TF|GF)@([[:alpha:]]|(_|-|\$|&|%|\*|!|\?))([[:alnum:]]|(_|-|\$|&|%|\*|!|\?))*/';
    $isLabel = '/^([[:alpha:]]|(_|-|\$|&|%|\*|!|\?))([[:alnum:]]|(_|-|\$|&|%|\*|!|\?))*$/';
    $beginsWithInt = '/int@.*$/i';
    $isInt = '/^int@[-+]?[0-9]*$/';
    $beginsWithBool = '/^bool@.*$/i';
    $isBool = '/^bool@(true|false)$/';
    $beginsWithString = '/^string@.*$/i';
    $isString = '/^string@(?(?=\\\[[:digit:]]{3}).|[^\\\#])*$/m';
    $isNil = '/^nil@nil$/';

    global $instructionArray, $stats, $list, $err, $errType, $valueTypes, $current;
    $argType = emptyArray();
    $current = $instructionArray[$stats->returnLines()];

    # Checking all the arguments in a loop
    for ($i = 1; $i <= $current->correctNumberOfArguments; $i++){
        $current->instr[0] = toUpper($current->instr[0]);
        $typeOfInstruction = $list->list[$current->instr[0]][$i];
        switch ($typeOfInstruction){
            case "type":
                if(in_array($tokens[$i], $valueTypes)){
                    array_push($argType,"type");
                }
                else {
                    $err->call($errType['other']);
                }
                break;

            case "sym":
                if (preg_match($beginsWithInt, $tokens[$i])) {
                    if (preg_match($isInt, $tokens[$i])){
                        array_push($argType, "int");
                        $current->instr[$i] = substr($tokens[$i], 4);
                    }
                    else {
                        $err->call($errType['other']);
                    }
                }
                elseif (preg_match($beginsWithBool, $tokens[$i])) {
                    if (preg_match($isBool, $tokens[$i])){
                        array_push($argType, "bool");
                        $current->instr[$i] = substr($tokens[$i], 5);
                    }
                    else {
                        $err->call($errType['other']);
                    }
                }
                elseif (preg_match($beginsWithString, $tokens[$i])) {
                    if (preg_match_all($isString, $tokens[$i])){
                        array_push($argType, "string");
                        if (strcmp ($current->instr[$i], "string@") == 0){
                            $current->instr[$i] = "";
                        }
                        else {
                            $current->instr[$i] = substr($current->instr[$i], -strlen($current->instr[$i])+7);
                        }
                    }
                    else {
                        $err->call($errType['other']);
                    }
                }
                elseif (preg_match($isVar, $tokens[$i])) {
                    array_push($argType, "var");
                    $current->instr[$i] = translateCharacters($current->instr[$i]);
                }
                elseif (preg_match($isNil, $tokens[$i])){
                    array_push($argType, "nil");
                    $current->instr[$i] = "nil";
                }
                /*
                elseif (preg_match($isLabel, $tokens[$i])) {
                    array_push($argType, "label");
                }*/
                else {
                    $err->call($errType['other']);
                }

                break;

            case "var":
                if (preg_match($isVar, $tokens[$i])){
                    array_push($argType, "var");
                }
                else {
                    $err->call($errType['other']);
                }
                break;

            case "lab":
                if (preg_match($isLabel, $tokens[$i])){
                    array_push($argType, "label");
                }
                else {
                    $err->call($errType['other']);
                }
                break;
        }
    }
    # Print the instruction with arguments onto stdout
    $instr = $current->instr;
    echo "    <instruction order=\"" . $stats->returnLines() . "\" opcode=\"" . toUpper($instr[0]) . "\">\n";
    for ($i = 1, $j = 0; $i < count($instr); $i++, $j++){
        $instr[$i] = translateCharacters($instr[$i]);
        echo "        <arg$i type=\"" . $argType[$j] . "\">$instr[$i]</arg$i>\n";

    }
    echo "    </instruction>\n";
}

# Check if the operation has correct amount of arguments 
function validateTokens($tokens){
    global $instructionArray, $stats, $list, $err, $errType;

    $current = $instructionArray[$stats->returnLines()];
    $tokens[0] = toUpper($tokens[0]);
    //fprintf(STDERR, $opcode);
    if (isInArray($tokens[0], $list->list)){
        $current->argCount($list->list[$tokens[0]][0]);
        if ($current->correctNumberOfArguments == (count($tokens) - 1)){
            validateParameters($tokens);
        }
        else {
            $err->call($errType['other']);
        }

    }
    else {
        $err->call($errType['opcode']);
    }

}

###########################################################################
########################### Checking arguments ############################
$longopts = array("help", "stats:", "comments", "loc", "labels", "jumps");
$options = getopt("", $longopts);
$err = new err();

if (validArguments("help", $options)){
    if ($argc == 2){
        fprintf(STDERR, $help);
        exit(SUCCESS);
    }
    else {
        $err->call($errType['other']);
    }
}

# Check if there is a file specified for stat arguments
if ((validArguments("loc", $options)) ||
    (validArguments("comments", $options)) ||
    (validArguments("labels", $options)) ||
    (validArguments("jumps", $options)) ){
    if (!validArguments("stats", $options)){
        $err->call($errType['params']);
    }
}


###########################################################################
####################### Loading the code from STDIN #######################
if ($input){
    $stats = new statistics(); // Instantiate the statistics object
    while (($line = fgets($input)) !== false){ // Load lines from stdin
        $code = trim($line);
        $code = preg_replace('/\s+/', ' ', $line);

        #Check for the initial .IPPcode19 header..
        if ($stats->numberOfLines == 0){
            # fprintf(STDERR,strval(preg_match("/(^\.IPPCODE19\s*#.*|^.IPPCODE19\s*$)/i"."\n", $code)));
            if (preg_match("/(^\.IPPCODE19\s*#.*|^.IPPCODE19\s*$)/i", $code) != 1){
                $err->call($errType['header']); #Call error if the error is missing
            }
            /*
             * If there is no error, print
             * XML header into stdout
             */
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<program language=\"IPPcode19\">\n";
            $list = new instructionList();
            $stats->numberOfLines++;
            continue;
        }

        # Check if the line is a comment
        if($stats->checkForComments($code)){
            $code = substr($code, 0, strpos($code, '#'));
        }

        # Increase line count only if the line is not empty
        if (preg_match("/^\s*$/", $code) != 1) {
            $stats->numberOfLines++;
        }

        else{
            continue;
        }

        $stats->checkForJump($code);
        $stats->checkForLabels($code);

        $tokens = explode(' ', trim($code)); # explode tokens into an array
        if ($stats->numberOfLines > 1){
            #Create an instance for every instruction (line of code)
            $instructionArray[$stats->returnLines()] = new instruction();
            $instructionArray[$stats->returnLines()]->getInstruction($tokens);
            validateTokens($tokens); # Check every line for correctness
        }
    }
    if ($stats->numberOfLines == 0) {
        $err->call($errType['header']); # If the file is empty -> It's missing a header -> 21
    }
}

############################################################################
echo "</program>\n"; # Print the finishing line of xml file

/*
 * This block is implemented for the STATP extension
 * It checks arguments an writes requested stats
 * into a file.
 */
if (validArguments("stats", $options)){
    $statFileName = $options["stats"];
    $statFile = fopen($statFileName, "w");
    if (!$statFile) {
        $err->call($errType['output']);
    }
    $stats->numberOfLines -= 1;
    foreach ($argv as $argChoice) {
        if (strcmp($argChoice, "--loc") == 0){
            fwrite($statFile, "$stats->numberOfLines\n");
        }
        elseif (strcmp($argChoice, "--comments") == 0){
            fwrite($statFile, "$stats->numberOfComments\n");
        }
        elseif (strcmp($argChoice, "--labels") == 0){
            fwrite($statFile, "$stats->numberOfLabels\n");
        }
        elseif (strcmp($argChoice, "--jumps") == 0){
            fwrite($statFile, "$stats->numberOfJumps\n");
        }

    }
    fclose($statFile);
}

/*
 *  Printing stats for debugging purposes
 */
if (DEBUG == 1){
    fwrite(STDERR, "Parser finished.\n");
    fwrite(STDERR, "Comments: " . strval($stats->numberOfComments) . "\n");
    fwrite(STDERR, "LOC: " . strval($stats->returnLines()+1) . "\n");
    fwrite(STDERR, "Jumps: " . strval($stats->numberOfJumps) . "\n");
    fwrite(STDERR, "Labels: " . strval($stats->numberOfLabels) . "\n");
    #var_dump($options);
}
fclose($input);

?>
