BookArtGenerator
================

A PHP script which will create a folding pattern for you if you provide it with an image.

Folding the pattern turns a dull book into real art.


The program file for 'BookArtGenerator' is BookArtGenerator.php. 

To run, it requires php 5.5 or higher (with GD library, which is usually included) and imagemagick.  
I can only debug the program on Linux, so if you have difficulties on Windows, you will need to help with debugging.
The version for Windows will only run in the Windows command line, the default version only runs in Unix-like environments.

How to use:

Make sure you have php 5.5 (http://www.php.net/) or later with the GD library installed.
On Windows, it may be easier to install WAMP, which includes php and saves you some configuration.
Also make sure you have imagemagick (http://www.imagemagick.org/script/index.php) installed. 

Copy the script (either BookArtGenerator.php for Linux or BookArtGenerator-Win.php for Windows) into a new directory on your computer.

Put a png file which fulfills the following requirements into the directory where the script is:

- Without transparency
- In RGB mode
- With a not very detailed, dark object 
- Object may contain holes, or consist of several simple shapes, but there may be no gaps which go from top of the picture to the bottom
- Object in front of a bright background
- Object's width-to-height ratio must be within reasonable limits, you cannot open a book endlessly ;-)
- If there are holes or gaps in the object, make sure that there are no more than 5 holes in every orthogonal line. Less will turn out better.

Examples of a good choice: continuously written short words, single letters, simple silhouettes, all black on white...  
Examples of a bad choice: photo, colourful drawings with many details, words with more than 6 letters,...


Make sure you have the rights to reproduce the picture (for example, take a public domain picture from http://openclipart.org/ ).

Make sure you have the rights to execute scripts in the script directory.   
On the command line, go into the directory where the script is and enter
 
php BookArtGenerator.php
or
php BookArtGenerator-Win.php

depending on the version you downloaded.
 
Answer the questions regarding your book.  
Be sure to have a ruler - and of course, the book - on hand. 

When the program has finished, to preview the result, look into the file named \*\_preview.png.
Don't worry too much about the aspect ratio, this will likely look distorted (too broad usually). Every page is represented by an orthogonal line. The spaces in between are just to separate the lines more clearly for easier controlling.   
You will be able to control the aspect ratio of the final result by opening your book wider or pushing it more closed.  
Only check if the details are all there and if the alternating page folding pattern creation has worked.  
To fold your pattern, follow the instructions in \*\_pattern.png.
 
If you make a beautiful object of art, please don't hesitate to send me a picture!

Please note the licence (GPLv3) in the separate file and at the top of the program script.
