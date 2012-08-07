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

There is absolutely no lifetime managment of these cache files, so it might
be a good idea to go in there from time to time and clean it out. Perhaps as
I learn more about phpBB I can figure out the right way to do this. 





Where am I?
-----------

You may be reading this as readme.txt in a distribution file. If not, you're
probably viewing it on my website. In either case, you can find 
[Documentation][] or [Source Code][]

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
-------------------

Apply the following patch. Change "/var/www/phpBB3" to whatever the root 
directory of PHPBB is. Note that this is at line 118 (at least with my version,
whichever that is). 

    118a119,120
    >       include("/var/www/phpBB3/latexml/phpbb_hook.php"); 
    > 
    
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





 