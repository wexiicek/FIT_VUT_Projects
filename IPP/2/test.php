<?php
define('DEFAULT_PARSER', "./parse.php");
define('DEFAULT_INTERPRETER', "./interpret.py");
define('DEFAULT_DIRECTORY', ".");
define('DEBUG', false);
define('RUNJEXAM', true);

$dir = DEFAULT_DIRECTORY; // Set up test directory
$parser = DEFAULT_PARSER; // Set up default parser file
$interpreter = DEFAULT_INTERPRETER; // Set up default interpreter file
$recursive = false; // Switch for recursive subfolder searching
$mode = "both"; // Mode in which the tests operates

$errTypes = array (
    'invalidArguments' => array(10, "Invalid arguments. Please run with --help.\n"),
    'fileNotExistent' => array(11, "Sorry, some files are not available.\n")
);


function call_error($error){
	/*
		Function prints the error message to standar error
		output and exits with corresponding value
	*/
    fwrite(STDERR, $error[1]);
    exit($error[0]);
}


function htmlInit() {
	/*
		Functions opens a HTML file
		It also prints the javascript functions to operate
		the buttons on the result page
		and the current datetime
	*/
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


function check_arguments($options){
	/*
		Functions goes through program arguments,
		checks their validity and sets the internal switches
		and variables if needed.
	*/
    global $mode, $argc, $errTypes, $dir, $parser, $interpreter, $recursive;
    if (count($options) != $argc-1) {
        call_error($errTypes['invalidArguments']);
    }

    if (array_key_exists("help", $options)){
        if ($argc == 2){
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
        }
        else {
            call_error($errTypes['invalidArguments']);
        }
    }

    if (array_key_exists("directory", $options)){
        $dir = $options["directory"];
    }

    if (array_key_exists("recursive", $options)){
        $recursive = true;
    }

    if (array_key_exists("parse-script", $options)) {
        $parser = $options["parse-script"];
    }

    if (array_key_exists("int-script", $options)) {
        $interpreter = $options["int-script"];
    }

    if (array_key_exists("parse-only", $options)) {
        if (array_key_exists("int-script", $options)){
            call_error($errTypes['invalidArguments']);
        }
        $mode = "parse-only";
    }

    elseif (array_key_exists("int-only", $options)) {
        if (array_key_exists("parse-script", $options)){
            call_error($errTypes['invalidArguments']);
        }
        $mode = "int-only";
    }
}


function get_files($recursive, $dir) {
	/*
		Returns the file names in the $dir
		directory (and it's subfolders, if recursive)
	*/
    if (DEBUG){
        var_dump($recursive);
        var_dump($dir);
    }

    # Looking for subdirectories recursively
    if ($recursive === true) {
        $files = shell_exec("find $dir -type f 2> /dev/null| grep .src$");
    }
    # Looking for tests only in the current directory
    else {
        $files = shell_exec("ls $dir 2> /dev/null| grep .src$ ");
    }
    return $files;
}


function external_python($interpreter, $dir, $test_name, $extension) {
	/*
		External script, which runs the interpreter with
		current test
	*/
    if (!(file_exists("$dir/$test_name.in"))) {
        $inFile = fopen("$dir/$test_name.in", "w");
        fclose($inFile);
    }
    return shell_exec("python3.6 \"$interpreter\" --source=\"$dir/$test_name.$extension\" --input=\"$dir/$test_name.in\" 1>\"$dir/$test_name.ires\" ; echo $?");
}


function external_php($parser, $dir, $test_name) {
	/*
		External script, which runs the parser with
		current test
	*/
    return shell_exec("php7.3 \"$parser\" <\"$dir/$test_name.src\" 1>\"$dir/$test_name.pres\"; echo $?");
}


function external_diff($dir, $test_name) {
	/*
		External script which compares two files
	*/
    if (!(file_exists("$dir/$test_name.out"))) {
        $outFile = fopen("$dir/$test_name.out", "w");
        fclose($outFile);
    }
    return shell_exec("diff \"$dir/$test_name.out\" \"$dir/$test_name.ires\" 2>/dev/null 1>/dev/null; echo $?");
}


function external_remove_file($dir, $test_name, $extension){
	/*
		Function that removes temporary files 
	*/
    return shell_exec("rm \"$dir/$test_name.$extension\"");
}


function external_jexamxml($dir, $test_name) {
	/*
		Function that compares two XML files (needs files)
		Or just run it on merlin
	*/
    return shell_exec("java -jar /pub/courses/ipp/jexamxml/jexamxml.jar \"$dir/$test_name.out\" \"$dir/$test_name.pres\" diffs.xml  /D /pub/courses/ipp/jexamxml/options 2>/dev/null 1>/dev/null; echo $?");
}


function testResHTML($dir, $current, $retVal, $refRetVal, $passed, $i, $msg){
	/*
		Function prints the test result with corresponding information
		to the standard output
		Test can be failed or passed, color of the block is changed if the test has failed
	*/
    $clr = $passed? "#2B7A78" : "#17252A"; # Background color of test div
    $class = $passed? "passed" : "failed"; # Set test class to pass or fail based on the result
    $green = $passed? "lightgreen" : "red"; # Set color of text to indicate failure
    $currIndex = $i + 1; # Index of a test
    echo
    "
    <div class=\"test $class\" style=\"background-color:$clr\">
        <h3><span style=\"color: $green\">$currIndex</span>: $dir/$current</h3>
        <input style=\"background-color: $clr\" type=\"button\" id=\"btn$i\" value=\"Expand\" onclick=\"showCurrent($i)\"></input>
        <div class=\"hidden\" id=\"$i\">
            <p>Expected result: <b> $refRetVal </b></p>
            <p>Actual result: <b> $retVal </b></p>
            <p>Context: <b>$msg</b></p>
        </div>
    </div>

    ";
}


function run_test($dir, $test_name, $mode, $i, $interpreter, $parser) {
	/*
		Runs test in specified mode, default is "both"
		But can be changed with the arguments

		Checks the reference retvals with current test retvals,
		compares the file with diff,
		compares the file with jexam,
		etc...
	*/
    if (DEBUG){
        fprintf(STDERR, "Running test no.: ".$i.") ".$test_name."\n");
    }

    if (strcmp($mode, "parse-only") == 0) {
        $parRetVal = external_php($parser, $dir, $test_name);

        if (!(file_exists("$dir/$test_name.rc"))){
            $refFile = fopen("$dir/$test_name.rc", "w");
            fwrite($refFile, "0");
            fclose($refFile);
        }
        
        if (file_exists("$dir/$test_name.rc")){
            $refFile = fopen("$dir/$test_name.rc", "r");
            $referenceParRetVal = intval(fgets($refFile));
        }
        if ($referenceParRetVal == $parRetVal) {
            if ($referenceParRetVal == 0 && ! $parRetVal == 0){
                if (RUNJEXAM){
                    $file_diff = external_jexamxml($dir, $test_name);
                    if ($file_diff != 0){
                        testResHTML($dir, $test_name, $parRetVal, $referenceParRetVal, false, $i, "Output files are different");                    
                    } 
                    else {
                        testResHTML($dir, $test_name, $parRetVal, $referenceParRetVal, true, $i, "OK");        
                    }   
                }
                else {
                    testResHTML($dir, $test_name, $parRetVal, $referenceParRetVal, true, $i, "OK"); 
                }
                if (DEBUG) {
                    fprintf(STDERR, $file_diff."\n");
                }
            }
            else {
            testResHTML($dir, $test_name, $parRetVal, $referenceParRetVal, true, $i, "OK");
            }
        }
        else {
            testResHTML($dir, $test_name, $parRetVal, $referenceParRetVal, false, $i, "[PARSER]: ");   
        }

        if (file_exists("$dir/$test_name.pres")){
            external_remove_file($dir, $test_name, "pres");
        }
    }

    else if (strcmp($mode, "int-only") == 0) {

        $intRetVal = external_python($interpreter, $dir, $test_name, "src");

        if (!(file_exists("$dir/$test_name.rc"))){
            $refFile = fopen("$dir/$test_name.rc", "w");
            fwrite($refFile, "0");
            fclose($refFile);
        }
        
        if (file_exists("$dir/$test_name.rc")){
            $refFile = fopen("$dir/$test_name.rc", "r");
            $referenceIntRetVal = intval(fgets($refFile));
        }
        if ($referenceIntRetVal == $intRetVal) {
            if ($referenceIntRetVal == 0 && $intRetVal == 0){
                $file_diff = external_diff($dir, $test_name);
                if ($file_diff != 0){
                    testResHTML($dir, $test_name, $intRetVal, $referenceIntRetVal, false, $i, "Output files are different.");                    
                } 
                else {
                    testResHTML($dir, $test_name, $intRetVal, $referenceIntRetVal, true, $i, "OK");        
                }
                if (DEBUG) {
                    fprintf(STDERR, $file_diff."\n");
                }   
            }
            else {
            testResHTML($dir, $test_name, $intRetVal, $referenceIntRetVal, true, $i, "OK");
            }
        }
        else {
            testResHTML($dir, $test_name, $intRetVal, $referenceIntRetVal, false, $i, "Interpret retval is different.");   
        }
        if (file_exists("$dir/$test_name.ires")){
            external_remove_file($dir, $test_name, "ires");
        }

    }

    else if (strcmp($mode, "both") == 0) {
    	if (DEBUG){
        	fprintf(STDERR, $test_name."\n");
        }
        $parRetVal = external_php($parser, $dir, $test_name);
        $intRetVal = external_python($interpreter, $dir, $test_name, "pres");

        if (!(file_exists("$dir/$test_name.rc"))) {
            $refFile = fopen("$dir/$test_name.rc", "w");
            fclose($refFile);
        }

        if (file_exists("$dir/$test_name.rc")) {
            $refFile = fopen("$dir/$test_name.rc", "r");
            $referenceRetVal = intval(fgets($refFile));
            fclose($refFile);
        }

        if (($referenceRetVal == $parRetVal) && ($referenceRetVal == $intRetVal)) {
            /*if ($referenceRetVal == 0 && $parRetVal == 0){
                if (RUNJEXAM){
                    $file_diff = external_jexamxml($dir, $test_name);
                    if ($file_diff != 0){
                        testResHTML($dir, $test_name, $parRetVal, $referenceRetVal, false, $i, "XML is different");
                        return;                    
                    } 
                }
                if (DEBUG) {
                    fprintf(STDERR, $file_diff."\n");
                }
            }*/
            if ($referenceRetVal == 0 && $intRetVal == 0) {
                $file_diff = external_diff($dir, $test_name);
                if ($file_diff != 0){
                    testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, false, $i, "Output files are different");
                    return;                    
                } 
                else {
                    testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, true, $i, "OK");        
                    return;
                }
                if (DEBUG) {
                    fprintf(STDERR, $file_diff."\n");
                }   
            }
        } 
        else {
            if ($referenceRetVal == $parRetVal && $referenceRetVal == $intRetVal){
                testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, true, $i, "OK");
                return;

            }
            else if ($referenceRetVal != $parRetVal && $parRetVal != 0) {
                testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, false, $i, "Parser retval is different.");
                return;
            }
            else if ($referenceRetVal != $intRetVal) {
                testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, false, $i, "Interpret retval is different.");
                return;
            }
            testResHTML($dir, $test_name, $intRetVal, $referenceRetVal, true, $i, "OK");
            return;
        }

        if (file_exists("$dir/$test_name.pres")){
            external_remove_file($dir, $test_name, "pres");
        }
        if (file_exists("$dir/$test_name.ires")){
            external_remove_file($dir, $test_name, "ires");
        }
    }

}


function main() {
	/*
		Function sets up the environment, creates global variables and
		initiates tests
		Also, it creates and closes the HTML file
	*/
    global $dir, $parser, $interpreter, $recursive, $errTypes, $mode;
    $longopts = array("help", "directory:", "recursive", "parse-script:", "int-script:", "parse-only", "int-only");
    $options = getopt("", $longopts);
    check_arguments($options);
    $file_string = get_files($recursive, $dir);
    $file_list = explode("\n", $file_string);
    if (DEBUG) {
        var_dump($file_list);
    }

    htmlInit();

    for ($i = 0; $i < (count($file_list)-1); $i++) {
        $test_name = preg_replace("/.src$/", "", $file_list[$i]);
        if (DEBUG){
        	fprintf(STDERR, $test_name."\n");
        }
        if ($recursive){
            $path = pathinfo($test_name);
            $dir = $path["dirname"];
            $test_name = $path["basename"];
        }
        if (DEBUG) {
            var_dump($dir);
            var_dump($test_name);
        }
        run_test($dir, $test_name, $mode, $i, $interpreter, $parser);
    }

    htmlClose(count($file_list)-1);
}

main();
