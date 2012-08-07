#!/usr/bin/perl
#  
#   latexml_strip: strips header/footer from latexml output and prefixes all
#                  style classes for inclusion in phpbb 
# 
#   Copyright (C) 2012 Josh Bialkowski (jbialk@mit.edu)
# 
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#   (at your option) any later version.
# 
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
# 
#   You should have received a copy of the GNU General Public License
#   along with this program.  If not, see <http://www.gnu.org/licenses/>.
#  

$pastBodyOpen =0;

while(<STDIN>)
{
    # We want to strip out everything up to and including the body tag, so 
    # simply ignore until we get there
    if($pastBodyOpen)
    {
        # We want to ignore everything including and after the last body tag
        # so when we get there we can simply exit (and flush)
        last if(/<\/body>/i);

        # Actually, break if we hit the latxml footer
        # last if(/LaTeXML-logo/i);
        
        # expand any xhtml div tags
        s#(<div[^>]+)/>#$1></div>#g;

        # otherwise we look for any class strings and replace them if
        # necessary
        s/class="([^"]+)"/my $x=$1; $x=~s#(\S+)#latexml-$1#g; "class=\"$x\""/ge;

        # and print the transformed output
        print $_;
    }
    elsif(/<body>/i)
    {
        $pastBodyOpen=1 ;
    }
}

