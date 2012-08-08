LaTeXML BBCodes for phpBB3
===========================

A method for using latexml to render the contents of "[tex][/tex]" bbcodes.
This is what proglass looks like in firefox with latexml:

![proglass screenshot](screenshot_proglass.png "Proglass with LaTeXML")

This is what prosilver looks like:

![prosilver screenshot](screenshot_prosilver.png "Prosilver with LaTeXML")




Caching
-----------
In order to prevent latexml being called every time the page is rendered, a
rudimentary caching mechanism is used. Every post gets a time-based uuid. When
the processing a tex bbcode, the hook script will first check to see if a 
cache file with that uuid and bbcode index exists. If so, it will simply 
read the contents of that file. If not, it will call latexml and latexmlpost
to generate that file, and then read the contents.

There is an additional hook called at the beginning of submit_post and 
delete_post which will delete these cache files. 




Security
-----------

LaTeXML will generate HTML from LaTeX sources. This can potentially open a 
huge security vulnerability for your site enabling all kinds of XSS attacks. 
I don't recommend using this unless you trust every one of your users enough 
to allow them to post any arbitrary html. 



Info
--------------------------

Copyright (C) 2012 Josh Bialkowski (jbialk@mit.edu)

This software is licensed under the GPL v3. You can find this documentation 
at [Documentation][] and the source code at [Source][]

[Documentation]: http://www.cheshirekow.com/~projects/phpbb_latexml/
[Source Code]:   git://git.cheshirekow.com/phpbb_latexml.git/





Install latexml
------------------- 

On a debian system:

    sudo apt-get install latexml
    
Or from source

    svn co https://svn.mathweb.org/repos/LaTeXML/trunk latexml
    cd latexml
    perl Makefile.PL --prefix=/usr
    make
    sudo make install
    
Patching latexml
-------------------

As of revision 2526 there seems to be a bug in latexmlpost for reading from
stdin. The bug report is at https://trac.mathweb.org/LaTeXML/ticket/1634.
It's an easy fix. Just apply the following patch:

    Index: lib/LaTeXML/Post.pm
    ===================================================================
    --- lib/LaTeXML/Post.pm (revision 2526)
    +++ lib/LaTeXML/Post.pm (working copy)
    @@ -397,7 +397,7 @@
       my $string;
       { local $/ = undef; $string = <>; }
       $options{sourceDirectory} = '.' unless $options{sourceDirectory};
    -  my $doc = $class->new(LaTeXML::Common::XML::Parser()->parseString($string),%options);
    +  my $doc = $class->new(LaTeXML::Common::XML::Parser->new()->parseString($string),%options);
       $doc->validate if $$doc{validate};
       $doc; }


Install the files
-------------------

Copy the latexml directory to your phpbb root directory
Edit phpbb_hook.php setting the following variables to their correct location:

    // adjust this to match your system configuration
    $latexml_path      = "/usr/local/bin/latexml";
    $latexmlpost_path  = "/usr/local/bin/latexmlpost";
    $latexmlstrip_path = "/var/www/phpBB3/latexml/latexml_strip.pl";



Setup includes/bbcode.php
---------------------------

Add the following on line 118 of includes/bbcode.php

    include("/var/www/phpBB3/latexml/phpbb_hook.php");
    
right before this line

    // Remove the uid from tags that have not been transformed into HTML
    $message = str_replace(':' . $this->bbcode_uid, '', $message);

Change "/var/www/phpBB3" to whatever the root 
directory of PHPBB is. This is the bbcode hook. It is called immediately after
the normal bbcode processing and extracts the "[tex][/tex]" tags, running their
contents through latexml and caching the input. 

    


Setup includes/functions_posting.php
-------------------------------------

Add the following to the top, immediately after the initial comment

    // includes latexml hook for deleting cached latexml output
    require_once('/var/www/phpBB3/latexml/phpbb_post_hook.php');

Add the following to the beginning of submit_post, right after "return false;"
on line 1637

   clear_latexml_caache($data['bbcode_uid']);

Add the following to the beginning of delete_post, right after the global
declarations on line 1384

    clear_latexml_caache($data['bbcode_uid']);


    
Create the BBCode
-------------------

Go to 

 * Admin Control Panel
     * Posting (tab)
         * BBCodes (on the left)
 
Create a new bbcode  like this:

    [tex]{TEXT}[/tex]
    
Leave the HTML Replacement blank. Check "Display on posting page". Then click
"submit"

Edit Templates
-------------------

For any active templates you have, you need to add a reference to the latexml
stylesheet. There is probably a correct way to do this, but I did manually. 
For each template (prosilver, proglass, prosilver special edition, prosilver 
wide), I add the following line to "overall_header.html"

    <link rel="stylesheet" type="text/css" href="/phpBB3/latexml/core.css"/>

I put it right before {META}





 