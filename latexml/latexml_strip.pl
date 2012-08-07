#!/usr/bin/perl

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

