<?php
# This function calculates execution time of a test
# It's implementation is based on StackOverflow forum, which can be
# found here: https://stackoverflow.com/a/535040
# Author: phihag @ StackOverflow
$exTime = getrusage();
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
        -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

# Defining default path to parser, interpreter
define('SUCCESS', 0);
define('ARGS', 10);
define('DEFAULT_PARSER', "./parse.php");
define('DEFAULT_INTERPRETER', "./interpreter.py");
define('DEFAULT_DIRECTORY', ".");

$dir = DEFAULT_DIRECTORY;
$parser = DEFAULT_PARSER;
$interpreter = DEFAULT_INTERPRETER;
$recursive = false;

# Testing mode
$testMode = array (
    'both' => 0,
    'int-only' => 1,
    'parse-only' => 2,
);

#Defining errors
$errTypes = array (
    'invalidArguments' => "Invalid arguments. Please run with --help.\n",
    'fileNotExistent' => "Sorry, some files are not available.\n"
);

# Defining possible arguments
$longopts = array("help", "directory:", "recursive", "parse-script:", "int-script:", "parse-only", "int-only");
$options = getopt("", $longopts);

function callError($retval, $desc){
    fwrite(STDERR, $desc."\n");
    exit($retval);
}

function argExists($arg) {
    global $options;
    return array_key_exists($arg, $options);
}

# Create HTML header with css and js functions
function htmlInit() {
    $date = date('d/m/Y h:i:s a', time());
    echo 
    "
<!DOCTYPE html>
<html>
<head>
    <title>IPP tests</title>
    <style type=\"text/css\">
        #header {
            margin-left: 10vw;
            border-radius: 10px;
            background-color: #DEF2F1;
            padding: 5px 0px 5px 0px;
            width: 80vw;
            justify-content: center;
            text-align: center;
        }
        .test {
            justify-content: center;
            background-color: red;
            width: 80vw;
            border-radius: 10px;
            text-align: center;
            color: white;
            padding-top: 10px;
            padding-bottom: 10px;   
            margin-top: 5px;
            margin-left: 10vw;
        }
        .hidden {
            display: none;
            text
        }
        .shown {
            display: block;
        }
        #exButton, #failButton, #passButton {
            background-color: #FEFFFF;
            border: 1px solid black;
            color:black;
            text-align: center;
            margin: auto;
        }
        body {
            background-color: #3AAFA9;
            font-family: Arial;
        }
        input {
            color: white;
            text-align: center;
            text-decoration: none;
            border: 1px solid black;
        }
        h3 {
            margin-top: 0px;
            margin-bottom: 3px;
        }
    </style>
    <script type=\"text/javascript\">
        function showCurrent(testNum) {
            var test = testNum.toString();
            var btn = \"btn\".concat(test);
            document.getElementById(test).classList.toggle(\"shown\");
            var button = document.getElementById(btn);
            if (button.value == \"Expand\") button.value = \"Reduce\";
            else button.value = \"Expand\";
        }

        
        
        function expandFailed() {
            var total = document.getElementById(\"totalTests\").textContent;
            var tests = document.getElementsByClassName(\"failed\");   
            if (tests.length == 0){
            	alert(\"There is nothing to be expanded\");
            	return;
            }         
            var button = document.getElementById(\"failButton\");
            var passButton = document.getElementById(\"passButton\");
            var allButton = document.getElementById(\"exButton\");
            for (i = 0; i < tests.length; i++) {
                if (button.value == \"Expand failed\"){
                    if (!tests[i].childNodes[5].classList.contains(\"shown\")){
                        if (passButton.value == \"Reduce passed\"){
                            allButton.value = \"Reduce all\";
                        }
                        tests[i].childNodes[5].classList.add(\"shown\");    
                    }
                }
                else {
                    if (tests[i].childNodes[5].classList.contains(\"shown\")){
                        allButton.value = \"Expand all\";
                        tests[i].childNodes[5].classList.remove(\"shown\");    
                    }
                }
                    
            }
            if (button.value == \"Expand failed\") button.value = \"Reduce failed\";
            else button.value = \"Expand failed\";
        }
        
        function expandPassed() {
            var total = document.getElementById(\"totalTests\").textContent;
            var tests = document.getElementsByClassName(\"passed\");   
            if (tests.length == 0){
            	alert(\"There is nothing to be expanded\");
            	return;
            }         
            var button = document.getElementById(\"passButton\");
            
            var allButton = document.getElementById(\"exButton\");
            var failButton = document.getElementById(\"failButton\");
            
            for (i = 0; i < tests.length; i++) {
                if (button.value == \"Expand passed\"){
                    if (!tests[i].childNodes[5].classList.contains(\"shown\")){
                        if (failButton.value == \"Reduce failed\"){
                            allButton.value = \"Reduce all\";
                        }
                        tests[i].childNodes[5].classList.add(\"shown\");    
                    }
                }
                else {
                    if (tests[i].childNodes[5].classList.contains(\"shown\")){
                        tests[i].childNodes[5].classList.remove(\"shown\");
                        allButton.value = \"Expand all\";    
                    }
                }
                    
            }
            if (button.value == \"Expand passed\") button.value = \"Reduce passed\";
            else button.value = \"Expand passed\";
        }
        
        function expandAll(){
            var total = document.getElementById(\"totalTests\").textContent;
            if (total == 0){
            	alert(\"There is nothing to be expanded\");
            }
            var tests = document.getElementsByClassName(\"test\");
            var button = document.getElementById(\"exButton\");
            var passButton = document.getElementById(\"passButton\");
            var failButton = document.getElementById(\"failButton\");
            if (button.value == \"Expand all\") {
                passButton.value = \"Reduce passed\";
                failButton.value = \"Reduce failed\";
                for (i = 0; i < total; i++) {
                    tests[i].childNodes[5].classList.add(\"shown\");
                }
            }
            else {
                passButton.value = \"Expand passed\";
                failButton.value = \"Expand failed\";
                for (i = 0; i < total; i++) {
                    tests[i].childNodes[5].classList.remove(\"shown\");
                }
            }
            
            
            if (button.value == \"Expand all\") button.value = \"Reduce all\";
            else button.value = \"Expand all\";
        }
        
        function passCount() {
        var total = document.getElementById(\"totalTests\").textContent;
        var passed = document.getElementsByClassName(\"passed\").length;
        var output = (((passed.toString()).concat(\" out of \")).concat(total.toString())).concat(\" passed.\");
        var placeholder = document.getElementById(\"passCount\").innerHTML = output;
        
        }        
    </script>
</head>
<body onload='passCount()'>
    <div id=\"header\">
        <h2> IPP Test Results </h2>
        <p> xjurig00 </p>
        <p> Generated: $date </p>
        <p id='passCount'>Tests are still running.. </p>
        <input type=\"button\" onclick=\"expandAll()\" value=\"Expand all\" id=\"exButton\">
        <input type=\"button\" onclick=\"expandPassed()\" value=\"Expand passed\" id=\"passButton\">
        <input type=\"button\" onclick=\"expandFailed()\" value=\"Expand failed\" id=\"failButton\"></div>
    </div>
    ";
}

# Close HTML file with closing tags
function htmlClose($total) {
    echo
    "
<span id=\"totalTests\" style=\"display:none;\">$total</span>
</body>
</html>";
}

# Functions opens shell and runs parser with PHP7.3
function runPHP($parser, $dir, $current) {
    $parse = "php7.3 \"$parser\" <\"$dir/$current.src\" 1>\"$dir/$current.pres\" 2>/dev/null; echo $?";
    return shell_exec($parse);
}

# Function opens shell and runs jexamxml to compare two files
function runJexam($dir, $current) {
    $jexam = "java -jar /pub/courses/ipp/jexamxml/jexamxml.jar $dir/$current.out $dir/$current.pres diffs.xml  /D /pub/courses/ipp/jexamxml/options 2>/dev/null 1>/dev/null; echo $?";
    return shell_exec($jexam);
}

# Function opens shell and runs interpreter with python3.6
function runPython($interpreter, $dir, $current, $extension) {
    $inter = "python3.6 \"$interpreter\" --source=\"$dir/$current.$extension\" --input=\"$dir/$current.in\" 1>\"$dir/$current.ires\" 2>/dev/null; echo $?";
    //$inter = "python3.6 \"$interpreter/interpret.py\" \"$dir/$current.rc\"; echo $?";
    return shell_exec($inter);
}

# Function removes temporary files used by testing script
function removeTempFile($file, $ext) {
    shell_exec("rm \"$file.$ext\"");
}

# Function opens shell and compares two files
function runDiff($dir, $current) {
    return shell_exec("diff -B \"$dir/$current.out\" \"$dir/$current.ires\"");
}

# Function that echoes a single test into HTML format
function testResHTML($dir, $current, $retVal, $refRetVal, $passed, $i){
    global $time, $prevtime;
    //fprintf(STDERR, $i.$current."\n");
    $currTime = $time - $prevtime; # Execution time
    $clr = $passed == "Yes"? "#2B7A78" : "#17252A"; # Background color of test div
    $class = $passed == "Yes" ? "passed" : "failed"; # Set test class to pass or fail based on the result
    $green = $passed == "Yes" ? "lightgreen" : "red"; # Set color of text to indicate failure
    $currIndex = $i + 1; # Index of a test
    echo
    "
    <div class=\"test $class\" style=\"background-color:$clr\">
        <h3><span style=\"color: $green\">$currIndex</span>: $dir/$current</h3>
        <input style=\"background-color: $clr\" type=\"button\" id=\"btn$i\" value=\"Expand\" onclick=\"showCurrent($i)\"></input>
        <div class=\"hidden\" id=\"$i\">
            <p>Expected result: <b> $refRetVal </b></p>
            <p>Actual result: <b> $retVal </b></p>
            <p>Execution time: <b> $currTime ms</b></p>
        </div>
    </div>

    ";
}

# Check if the return code bigger than 0 was expected
# Receive value from .rc file
# If this file does not exist, create it with value 0
function expectedFail($current, $dir, $retVal, $i) {
    if (file_exists("$dir/$current.rc")){
        $file = fopen("$dir/$current.rc", "r");
    }
    else {
        $file = fopen("$dir/$current.rc", "w");
        fwrite($file, "0");
    }
    if (!($file)){
        #TODO expected value is 0 ... exit();
        testResHTML($dir, $current, $retVal, 0, "Yes", $i);
        return;
    }

    $refRetVal = intval(fgets($file));

    fclose($file);

    if ($retVal == $refRetVal) {
        testResHTML($dir, $current, $retVal, $refRetVal, "Yes", $i);
    }
    else {
        testResHTML($dir, $current, $retVal, $refRetVal, "No", $i);
    }
    return;
}

# This functions runs the current test based on a chosen mode
function runTest($current, $mode, $i) {
    global $parser, $interpreter, $dir, $mode;
    #if (!(file_exists($parser) && file_exists($interpreter) && file_exists($current))){
    #    callError(11, $errTypes['fileNotExistent']); #TODO retvals everywhere
    #}
    if ($mode == 0 || $mode == 2) {
        $parserRetVal = intval(runPHP($parser, $dir, $current));
        if ($parserRetVal != 0){
            #TODO error
            expectedFail($current, $dir, $parserRetVal, $i);
            if (file_exists("$dir/$current.pres")){
                removeTempFile("$dir/$current", "pres");
            }
            return;
        }
    }

    # Mode is set to "both" or "int only"
    if ($mode == 0 || $mode == 1) {
        if (!file_exists("$dir/$current.in")){
            $inFile=fopen("$dir/$current.in", "w");
            fclose($inFile);
        }
        if (!file_exists("$dir/$current.rc")){
            $rcFile=fopen("$dir/$current.rc", "w");
            fclose($rcFile);
        }
        if ($mode == 0) {
            $interpreterRetVal = runPython($interpreter, $dir, $current, "pres", );
        }
        else {
            $interpreterRetVal = runPython($interpreter, $dir, $current, "src");
            //fprintf(STDERR, "Hodnota interpretu: $interpreterRetVal");
        }

        if($interpreterRetVal != 0) {
            #TODO error

            expectedFail($current, $dir, $interpreterRetVal, $i);
            if (file_exists("$dir/$current.ires")) {
                removeTempFile("$dir/$current", "ires");
            }
            return;
        }
    }

    $jexam = 0;
    if (file_exists("$dir/$current.out")){
        if ($mode == 2){
            clearstatcache();
            $size = filesize("$dir/$current.out");
            if ($size) {
                $jexam = intval(runJexam($dir, $current));
                if ($jexam != 0) {
                    testResHTML($dir, $current, "XML does not match.", null, "No", $i);
                }
                //fprintf(STDERR, $jexam);
            }
            else {
                    $presSize = filesize("$dir/$current.pres");
                    if ($presSize != $size){
                        fprintf(STDERR, "Files are different\n");
                    }
            }
        }
        else if (!$mode || $mode == 1){
            #compare outputs
        }
    }
    else {
        $outFile = fopen("$dir/$current.out", "w");
        fclose($outFile);
    }

    if (file_exists("$dir/$current.rc")){
        $resOut = fopen("$dir/$current.rc", 'r');
    }
    else {
        $resOut = 0;
    }

    if (!($resOut)){
        $refRetVal = 0;
    }
    else {
        $refRetVal = intval(fgets($resOut));
        fclose($resOut);
    }

    if ($mode == 0 || $mode == 1 && !$jexam){

        if($interpreterRetVal || $parserRetVal) {
            $diffRes = runDiff($dir, $current);
            $diffRes = "";
        }
    else {
        }
        if (strcmp($diffRes, "") == 0){
            # Test passed
            testResHTML($dir, $current, $interpreterRetVal, $refRetVal, "Yes", $i);
        }
        else {
            # Test didnt pass
            testResHTML($dir, $current, $interpreterRetVal, $refRetVal, "No", $i);
        }
    }

    # Mode is "both" or "parse only"
    elseif ($mode == 0 || $mode == 2 && !$jexam) {
        if ($parserRetVal == $refRetVal) {
            testResHTML($dir, $current, $parserRetVal, $refRetVal, "Yes", $i);
        }
        else {
            testResHTML($dir, $current, $parserRetVal, $refRetVal, "No", $i);
        }
    }
    # Mode is "both" or "int only"
    if ($mode == 0 || $mode == 1) {
        if (file_exists("$dir/$current.pres")) {
            removeTempFile("$dir/$current", "ires");
        }
    }

    # Mode is "both" or "parse only"
    if ($mode == 0 || $mode == 2) {
        if (file_exists("$dir/$current.pres")){
            removeTempFile("$dir/$current", "pres");
        }
    }
    return;

}

if (count($options) != $argc-1){
    callError(ARGS, $errTypes['invalidArguments']);
}
    # Print help
    if (argExists("help")) {
        if ($argc == 2) {
            echo "
This script is used for evaluation of parser/interpreter for IPPcode19.
There are 3 possible modes of use:
    run with: --parse-only  - Runs only the parser.
    run with: --int-only    - Runs only the interpreter.
    run without either of the previous - Runs parser and interpreter.
Other possible modifiers:
    --recursive     - Runs all tests that can be found in the specified folder and it's subfolders.
    --parse-script= - Specify path to parser.
    --int-script=   - Specify path to interpreter.
    --directory=    - Specify base directory of tests.
Works with up to 4 different file types.
*.src   - IPPcode19 for parser / XML representation for interpreter.
*.rc    - Expected return value for the test.
*.in    - Input for the interpreter.
*.out   - Expected output.
";
            exit(SUCCESS);
        }
        callError(ARGS, $errTypes['invalidArguments']);
    }

    # Looking for arguments
    if (argExists("directory")) {
        $dir = $options["directory"];
    }

    if (argExists("recursive")) {
        $recursive = true;
    }

    if (argExists("parse-script")) {
        $parser = $options["parse-script"];
    }

    if (argExists("int-script")) {
        $interpreter = $options["int-script"];
    }

    if (argExists("parse-only")) {
        if (argExists("int-script")){
            fprintf(STDERR, $errTypes['invalidArguments']);
            exit(ARGS);
        }
        $mode = $testMode['parse-only'];
    }

    elseif (argExists("int-only")) {
        if (argExists("parse-script")){
            fprintf(STDERR, $errTypes['invalidArguments']);
            exit(ARGS);
        }
        $mode = $testMode['int-only'];
    }

    else {
        $mode = 0; //both
    }

    # Looking for subdirectories recursively
    if ($recursive === true) {
        $files = shell_exec("find $dir -type f 2> /dev/null| grep .src$");
    }
    # Looking for tests only in the current directory
    else {
        $files = shell_exec("ls $dir 2> /dev/null| grep .src$ ");
    }

    htmlInit();
    $filesToTest = explode("\n", $files); # Expode file list into an array
    //fprintf(STDERR, strval($mode)."\n");
    $prevtime = 0; # Measuring execution time
    for ($i = 0; $i < (count($filesToTest)-1); $i++) {
        $ru = getrusage();
        $time = rutime($ru, $exTime, "utime");
        $current = preg_replace("/.src$/", "", $filesToTest[$i]);
        if ($recursive){
            $path = pathinfo($current); # Get path to current test
            $dir = $path['dirname']; # Split path and receive dir path
            $current = $path['basename']; # Split path and receive file name
        }
        runTest($current, $mode, $i); # Run test for current file
        $prevtime = $time;
    }

    htmlClose(count($filesToTest)-1);
