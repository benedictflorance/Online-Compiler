<!DOCTYPE HTML>
<?php
   include("configure.php");
   ini_set('display_errors', 1); 
   ini_set('log_errors',1); 
   error_reporting(E_ALL); 
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
   date_default_timezone_set('Asia/Kolkata');
session_start();
$submitErr=$fileErr=$titleErr='';
echo "<head><title>CompileCode-#1 compiler tool since 2017</title><link href=\"paste.css\" type=\"text/css\" rel=\"stylesheet\"/>
<link href=\"https://fonts.googleapis.com/css?family=Open+Sans\" rel=\"stylesheet\">
<link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'><head><body><div class=\"outer\">
<div class=\"middle\">";
$code='';
$title='';
$output1='';
$output2='';
$flag=0;
if(isset($_SESSION['username']))
{	
if(isset($_POST['submit'])){
	$wait=0;
	$query="SELECT * FROM processes";
    $qresult=mysqli_query($conn,$query);
    if (mysqli_num_rows($qresult)<3) {
    	$wait=mysqli_num_rows($qresult)-3;
    	$flag=1;
    }
	if($flag)
	{
	$token=uniqid();
	$sql =$conn->prepare("INSERT INTO processes(request) VALUES(?)");
	$sql->bind_param("s",$token);
	$result=$sql->execute();
	$errors=0;
	$file='';
	$code=$_POST['code'];
	$title=$_POST['title'];
	$username=$_SESSION['username'];
	$language=$_POST['language'];
	$name=$_FILES['filedoc']['name'];  
    $temp_name=$_FILES['filedoc']['tmp_name'];  
    if(isset($name)){
        if(!empty($name)){      
            $file=file_get_contents($temp_name);
            }
        $ext= pathinfo($name,PATHINFO_EXTENSION);
        }       
	if(empty($code)&&empty($name))
		{$fileErr="Either code/file should be given";
		 $errors++;}
	if(!empty($code)&&!empty($name))
		{$fileErr="Both code and file isn't allowed";
		 $errors++;}
	if(empty($code)&&!empty($name))
	if(!($ext=="c"||$ext=="cpp"||$ext=="py"||$ext=="doc"||$ext=="docx"||$ext=="txt"))
	{
		$fileErr="Invalid file format. Try again.";
		$errors++;
	}
	if(!$errors)
	{   
		$content=uniqid();
		if($language=='c'||$language=='cpp')
		{if(!empty($code))
		{
			file_put_contents('C:\MinGW\bin\\'.$content.'.'.$language, $code);
		}
		else
		{
		   file_put_contents('C:\MinGW\bin\\'.$content.'.'.$language, $file);
		}
		if(!empty($title))
		file_put_contents('C:\MinGW\bin\\'.$content.'.txt', $title);
		chdir('C:\MinGW\bin');
	}
	else if($language=='py')
	{
		if(!empty($code))
		{
			file_put_contents('C:\Users\bened\AppData\Local\Programs\Python\Python36-32\\'.$content.'.'.$language, $code);
		}
		else
		{
		   file_put_contents('C:\Users\bened\AppData\Local\Programs\Python\Python36-32\\'.$content.'.'.$language, $file);
		}	if(!empty($title))
		file_put_contents('C:\Users\bened\AppData\Local\Programs\Python\Python36-32\\'.$content.'.txt', $title);	
		chdir('C:\Users\bened\AppData\Local\Programs\Python\Python36-32\\');
	}
		if($language=='c')
		{
		$output1=shell_exec('gcc '.$content.'.c -o '.$content.'.exe 2>&1');

		if(empty($output1))
		{
		if(!empty($title))
				$output2=shell_exec($content.'.exe < '.$content.'.txt');	
		else
				$output2=shell_exec($content.'.exe');
		}
		exec('del '.$content.'.c');
		exec('del '.$content.'.exe');
		exec('del '.$content.'.txt');

		}
		else if($language=='cpp'){
		$output1=shell_exec('g++ '.$content.'.cpp -o '.$content.'.exe 2>&1');
		if(empty($output1))
		{
		if(!empty($title))
				$output2=shell_exec($content.'.exe < '.$content.'.txt');
		else
				$output2=shell_exec($content.'.exe');
		}
		exec('del '.$content.'.cpp');
		exec('del '.$content.'.exe');
		exec('del '.$content.'.txt');
		}
		else if($language=='py')
		{
		if(!empty($title))
		$output2=shell_exec('python '.$content.'.py < '.$content.'.txt 2>&1');
		else
		$output2=shell_exec('python '.$content.'.py 2>&1');
		exec('del '.$content.'.py');
		exec('del '.$content.'.txt');
		}
	}
	$query="DELETE FROM processes WHERE request='".$token."'";
    $qresult=mysqli_query($conn,$query);
	$flag=0;
	}
	else
	{
		$output2="You are number ".$wait." in the queue. Please be patient!";
	}
}
	echo "<a href =\"logout.php\" id=\"button\" class=\"green left\">Logout</a>
	<h1>CompileCode &copy</h1><h5>#1 paste tool since 2017</h5><h2 >Welcome, ".ucwords($_SESSION['name'])."!</h2>
	<form action=\"";echo htmlentities($_SERVER["PHP_SELF"]);echo "\" method=\"post\" enctype=\"multipart/form-data\">
	<h2>Compile Code</h2>
	<span class=\"success\">";echo $submitErr;echo "</span><br>
	<span style=\"color:red\">All * fields are mandatory</span><br>
	<div id=\"l\">
	Code:
	<textarea spellcheck=\"false\" onkeyup=\"this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';\" name=\"code\">";echo $code;echo "</textarea><br>
	<label> Input:<textarea spellcheck=\"false\" class=\"reduce\" onkeyup=\"this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';\" name=\"title\">";echo $title;echo "</textarea></label><br><span class=\"error\">";echo $titleErr;echo"</span><br>
	<label> Upload File?  <input type = \"file\" name = \"filedoc\"/></label><br><span class=\"error\">";echo $fileErr;echo"</span><br>
	Language:<span style=\"color:red\">*</span><select name=\"language\">
				<option value = \"c\">C</option>
				<option value = \"cpp\">C++</option>
				<option value = \"py\">Python</option>
			</select><br>
	<input id=\"button\"class=\"red\" type =\"submit\" class=\"red\" name=\"submit\" value = \"Compile\"/><br>
	</div>
	<div id=\"r\">
	Output: 
	<div spellcheck=\"false\" name=\"output\" id=\"output\">";echo $output1.$output2;echo "</div><br>
	</div>
	</form>";

}
else
	echo "<h1>Access Denied</h2><br><a id=\"button\" class=\"green\" href=\"login.php\">Click here to log in</a></div></div></body></html>";
?>