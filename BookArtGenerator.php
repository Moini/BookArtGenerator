<?php
//This program requires php 5.5 (cli) and imagemagick (convert)
//
//		Licence
//		================
//     <Book Art Creator: Creates patterns from png images for folding book pages to get book sculptures >
//     Copyright (C) 2014  Maren Hachmann
// 
//     This program is free software: you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation, either version 3 of the License, or
//     (at your option) any later version.
// 
//     This program is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with this program.  If not, see <http://www.gnu.org/licenses/>.

//		You can reach the author at marenhachmann at-sign yahoo.com.

//How to use:
// Make sure you have php 5.5 or later installed. Also make sure you have imagemagick installed. Put a png file (without transparency) of the picture you want on your book into the directory where the script is. Make sure you have the rights to execute scripts in that directory. On the command line, go into the directory where the script is and enter
// 
// php BookArtGenerator.php
// 
// Answer the questions regarding your book. Be sure to have a ruler on hand. To preview the result, look into the file named *_preview.png. To fold your pattern, follow the instructions in *_pattern.png.
// 
// If you make a beautiful object of art, please don't hesitate to send me a picture!


function getParametersFromUser()
{
	//get number of first page
	echo "Please tell me the number of the first even page in your book!\n";
	
	while (TRUE)
	{
		echo "Number of first even page: ";
		$fp = fopen("php://stdin","r");
		$answer_array = explode( " " , trim(fgets($fp)));
		if (count($answer_array) == 1 && is_numeric($answer_array[0]) && ($answer_array[0] >= 0) && ($answer_array[0]%2 == 0))
		{
			break;
		}
		else
		{
			echo "\nSorry, you must enter a valid page number (e.g. 8)!\n";
		}
	}
	
	$firstPageNumber = $answer_array[0];
	
	//get number of last page
	
	echo "\nNow, please tell me the number of the last even page of your book!\n";
	
	while (TRUE)
	{
		echo "Number of last even page: ";
		$lp = fopen("php://stdin","r");
		$answer_array = explode( " " , trim(fgets($lp)));
		if (count($answer_array) == 1 && is_numeric($answer_array[0]) && ($answer_array[0] > $firstPageNumber) && ($answer_array[0]%2 == 0))
		{
			break;
		}
		else
		{
			echo "\nSorry, you must enter a valid page number (e.g. 380)!\n";
		}
	}
	
	$lastPageNumber = $answer_array[0];
	
	//get height of book
	echo "\nNow I need to know the height of your book pages. It doesn't matter if the unit is cm or inch (only type in the number), as long as you stay consistent. Use the dot as decimal separator, if necessary.\n";
	
	while (TRUE)
	{
		echo "Height of book: ";
		$bh = fopen("php://stdin","r");
		$answer_array = explode( " " , trim(fgets($bh)));
		if (count($answer_array) == 1 && is_numeric($answer_array[0]) && ($answer_array[0] >= 0))
		{
			break;
		}
		else
		{
			echo "\nSorry, you must enter a valid measurement!\n";
		}
	}
	
	$heightOfBook = $answer_array[0];
	
	//get picture name
	echo "\nNow I need to know the name of the picture you would like to put on your book. Please put the picture in png-Format into the directory where this script is. 
=> The picture must be in black and white (if not, all other colors will be converted to pure black and white) and the colormode must be RGB,
=> The picture object must be dark, the background bright,
=> the ratio width to height may not be too big if you want it to look good (your picture cannot be very wide), 
=> the dark parts must all be connected, there may be holes, but I suggest it better not be more than 2, or 3 in any orthogonal line at max.\n";
	
	while (TRUE)
	{
		echo "Filename: ";
		$fn = fopen("php://stdin","r");
		$answer_array = explode( " " , trim(fgets($fn)));
		if (count($answer_array) == 1 && (preg_match("/.+\.png\z/i", $answer_array[0]) == TRUE))
		{			
			$image = @ImageCreateFromPNG($answer_array[0]); /* Versuch, Datei zu öffnen */
			if (!$image)
			{ 
				echo "\nCouldn't open file!\n";
			}
			else
			{
				imagedestroy($image);
				break;
			}
		}
		else
		{
			echo "\nSorry, you must enter a valid file name!\n";
		}
	}
	
	$pictureName = $answer_array[0];
	
	echo "\nYou entered these data: 
number of first page: $firstPageNumber
number of last page: $lastPageNumber
height of book: $heightOfBook
picture file name: $pictureName\n";

return array($firstPageNumber, $lastPageNumber, $heightOfBook, $pictureName);

}

function createTempPNG($params_array)
//creates an image called tempfile.png from the user data
{
	$first = $params_array[0];
	$last = $params_array[1];
	$height = $params_array[2];
	$filename = $params_array[3];
	$numPages = ($last - $first)/2 + 1;
	$imageheight = 100 * $height; //so the output is exact by two decimals after the decimal point, e.g. 10.5 cm/inch
	
	//convert image to correct size and colorspace
	$output = array("blubb");//only necessary as a placeholder
	$return_var = "bla";

	$imageMagickCommand = "convert $filename -resize ".$numPages."x".$imageheight."\! -threshold 50% tempfile.png";
	exec($imageMagickCommand, $output, $return_var);
	if ($return_var === 1)
	{
		echo "\nSomething went wrong while converting your image, please try something else!\n";
		exit;
	}

}

function createBasePattern($params_array)
{
	//set variables
	$first = $params_array[0]; //page number
	$last = $params_array[1]; //page number
	$height = $params_array[2]; //of book
	$filename = $params_array[3];
	$numPages = ($last - $first)/2 + 1;
	$imageheight = 100 * $height; //so the output is exact by two decimals after the decimal point, e.g. 10.5 cm/inch
	
	//open black and white image
	$image = ImageCreateFromPNG("tempfile.png");
	
	//white = 1 and black = 0
	$pattern = array();
	
	for ($x = 0; $x < $numPages; $x++) //loop through width/columns
	{	
		$colorAbove = "None"; //reset for beginning of a column
		$changes = FALSE;
		$numColorBands = -1;
		for ($y = 0; $y < $imageheight; $y++)//loop through height
		{

			$currentColor = imagecolorat($image ,$x ,$y);
			
			if ($colorAbove === "None" and $currentColor == 0)//if picture is black at the top border create start of a color band
			{
				$colorAbove = $currentColor;
				$numColorBands += 1;
				$pattern[$x][$numColorBands] = array("start"=>0);
				$changes = TRUE;
			}
			elseif ($currentColor == 0 and $y == $imageheight-1) //create end mark if the bottom pixel is black
			{
				$pattern[$x][$numColorBands]["end"] = $y+1;
				$changes = TRUE;
			}
			elseif ($colorAbove === "None" or $colorAbove == $currentColor)//do nothing at begin of column if the first pixel is white, or if color doesn't change
			{
				$colorAbove = $currentColor;
				continue;
			}
			elseif ($colorAbove == 1 and $currentColor == 0) //create folding mark if color goes from white to black
			{
				$colorAbove = $currentColor;
				$numColorBands += 1;
				$pattern[$x][$numColorBands] = array("start"=>$y);
				$changes = TRUE;
			}
			elseif ($colorAbove == 0 and $currentColor == 1) //create folding mark if color goes from black to white
			{
				$colorAbove = $currentColor;
				$pattern[$x][$numColorBands]["end"] = $y+1;
				$changes = TRUE;
			}

		}
		if ($changes == FALSE)
			{
				$pattern[$x] = "nofolds"; //mark pages with no folds, these will only be allowed at start or end of pattern
			}
	}

	return ($pattern);

}

function holesCheckPassed($pattern)
//checks if there are holes in the pattern (e.g. white columns other than at start or end of picture)
{
	//there may be up to 2 empty parts in a pattern, namely at its start and end
	$numEmpty = 0;
	$numEmptyAllowed = 0;
	if ($pattern[0] == "nofolds")
	{
		$numEmptyAllowed += 1;
	}
	if ($pattern[count($pattern)-1] == "nofolds")
	{
		$numEmptyAllowed += 1;
	}
	
	
	foreach ($pattern as $index => $value)
    {
		if ($value == "nofolds")
		{
			if ($index == 0)
			{
				$numEmpty += 1;
			}
			
			elseif ($pattern[$index-1] != "nofolds")
			{
				$numEmpty += 1;		
			}
		}    
    }

    if ($numEmpty == $numEmptyAllowed)
    {
		echo "\nCheck for holes passed!\n";
		return True;
    }
    elseif ($numEmpty < $numEmptyAllowed)
    {
		echo "\nSorry, but your picture is either all white or contains transparency!\n";
		return False;
    }
    elseif ($numEmpty > $numEmptyAllowed)
    {
		echo "\nSorry, but your picture has holes (like space between letters, for example) in it, this won't look good!\nPlease give me another picture!\n";
		return False;
    }

}

function createFinalPattern($pattern)
//create folding pattern which allows for alternate folding if there are several bands of dark in a line
{
	$finalPattern = array();
	foreach ($pattern as $column => $bandslist)
	{
		$numBands = count($bandslist);
		if ($numBands == 1)
		{
			if ($bandslist != "nofolds")
			{
				$finalPattern[$column] = $bandslist;
			}
			continue;
		}
		elseif ($numBands > 6) 
		{
			echo "\nYour picture has an awful lot of detail! This results in more than 6 alternating folds in some area(s). Please reduce the details in your picture and call this program again.\n";
			exit;
		}
		else
		{
			$finalPattern[$column] = [$bandslist[$column%$numBands]];
		}	
	}
	return ($finalPattern);
}

function createPreview($finalPattern, $imageheight, $previewWidth, $filename)
{
	//header ("Content-type: image/png");
	$previewImage = @imagecreatetruecolor($previewWidth, $imageheight*10)
      or die ("\nCould not create preview image!\n");
	$white = ImageColorAllocate ($previewImage, 255, 255, 255);
	$black = ImageColorAllocate ($previewImage, 0, 0, 0);
	imagefill($previewImage, 0, 0, $white);
	foreach ($finalPattern as $column => $bandslist)
	{
		imageline($previewImage, $column*3-1, ($bandslist[0]["start"]), $column*3-1, ($bandslist[0]["end"]-1), $black);	
		#imageline($previewImage, $column*2-1, ($bandslist[0]["start"])*2, $column*2-1, ($bandslist[0]["end"]-1)*2, $black);	
	}
	$imagename = preg_replace( "/\.png\z/i" , "" , $filename)."_preview.png";
	ImagePNG ($previewImage, $imagename);
}


function createTextFile($finalPattern, $filename, $offset)
//create text file for printing out
{
	$textfilename = preg_replace( "/\.png\z/i" , "" , $filename)."_pattern.txt";
	$textfile = fopen($textfilename, 'w') or die("can't open file");
	
	$string = "Book Folding Art Pattern for the Picture \"$filename\"
==============================================================================

Instructions:

These measurements describe where you will have to fold the pages of your book.
All measurements are given in cm/inch, whichever you chose at the beginning.
The first number indicates the page number, the second tells you where
(measured from the top of the book) you have to fold the upper corner down,
the third tells you where you will have to fold the lower corner up.

Page	     Top Fold	     Bottom Fold
==========================================\n\n";

	foreach ($finalPattern as $column => $contents)
	{
		$upperCorner = $contents[0]["start"]/100;
		$lowerCorner = $contents[0]["end"]/100;
		$pagenum = $column*2 + $offset;
		
		$string = $string."$pagenum		$upperCorner		$lowerCorner\n";
		
		if ($pagenum%10 == 0)
		{
			$string = $string."------------------------------------------\n";		
		}
	
	}
	
	$string = $string."\n\n\nThis pattern was created using the program BookFoldingArt.
The program is licenced under the GPLv3.

------------  HAVE FUN FOLDING :-)  ------------ !"; 
	fwrite($textfile, $string);
	
	fclose($textfile);
	return;
}

function runBAG()
{
	echo "\nWelcome to your BookArtGenerator!
---------------------------------";
	echo "\n\n     BookArtGenerator  Copyright (C) 2014  Maren Hachmann
     This program comes with ABSOLUTELY NO WARRANTY.
     This is free software, and you are welcome to redistribute it
     under certain conditions, please look into the script file for details.\n\n";
	
	//get parameters of book from user
	$params_array = getParametersFromUser();
	
	//create black and white picture with the correct dimensions
	createTempPNG($params_array);
	
	//create basic pattern
	$pattern = createBasePattern($params_array);
	
	//set variables
	$first = $params_array[0];
	$last = $params_array[1];
	$height = $params_array[2];
	$filename = $params_array[3];
	$numPages = ($last - $first)/2 + 1;
	$imageheight = 10 * $height; //so the output is exact by one decimal after the decimal point, e.g. 10.5 cm/inch
	$previewWidth = 3 * $numPages;
	$offset = $first; //describes the relation between page number and picture column number
	
	//check for holes in pattern
	if (!holesCheckPassed($pattern))
	{
		exit;
	}

	//create folding pattern which allows for alternate folding if there are several bands of dark in a line
	$finalPattern = createFinalPattern($pattern);
	
	//create preview picture
	createPreview($finalPattern, $imageheight, $previewWidth, $filename);
	
	//create text file for printing out
	createTextFile($finalPattern, $filename, $offset);
	
	//tell what to do now
	$previewfilename = preg_replace( "/\.png\z/i" , "" , $filename)."_preview.png";
	$patternfilename = preg_replace( "/\.png\z/i" , "" , $filename)."_pattern.txt";
	echo "\nTo preview your result, look at the file $previewfilename in the directory of the script.
If you like the result, fold it following the instructions in $patternfilename.\n\n
See you soon for another book!\n\n";
}

runBAG();

?>
