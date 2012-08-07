<?php
/**
 *  phpbb_latexml: provides bbcode hook for [tex][/tex] tags
 *  Copyright (C) 2012 Josh Bialkowski (jbialk@mit.edu)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// include this file in line 118 of includes/bbcode.php
// right before the comment where it says it strips UIDs from unparsed
// bbcode tags

// --------------------------------------------------------------------------------------------------
// adjust this to match your system configuration
$latexml_path      = "/usr/local/bin/latexml";
$latexmlpost_path  = "/usr/local/bin/latexmlpost";
$latexmlstrip_path = "/var/www/phpBB3/latexml/latexml_strip.pl";
$latexmlcache_path = "/var/www/phpBB3/latexml/cache";

// --------------------------------------------------------------------------------------------------
preg_match_all("#\[tex:$bbcode_uid\](.*?)\[/tex:$bbcode_uid\]#si",
                $message,
                $tex_matches);


for ($i=0; $i < count($tex_matches[0]); $i++) 
{
    $cache_file = sprintf("%s/%s.%d",$latexmlcache_path,$bbcode_uid,$i);
    $pos        = strpos($message, $tex_matches[0][$i]);

    if( !file_exists( $cache_file ) )
    {
        $latex_formula = html_entity_decode($tex_matches[1][$i]);
        
        $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                2 => array("file", "/tmp/latexml-php-hook.log", "a") // stderr is a file to write to
        );
        
        // run latexml
        $cmd = sprintf(
            "%s - | %s "
                ."--nomathimages "
                ."--nographicimages "
                ."--nopictureimages "
                ."--nodefaultcss "
                ."--novalidate "
                ."--format=xhtml "
                ."- | %s > %s ",
                $latexml_path,
                $latexmlpost_path,
                $latexmlstrip_path,
                $cache_file );
        $process = proc_open( $cmd, $descriptorspec, $pipes );
        
        if( is_resource($process) )
        {
            // write the formulat to the output
            fwrite($pipes[0], sprintf( "
                    \documentclass{article}
                    \usepackage{amsmath}
                    \begin{document}
                    %s
                    \end{document}
                    ", $latex_formula) );
            fclose($pipes[0]);
            
            $html = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $return_value = proc_close($process);        
        }
        else
            break;
    }

    $html    = file_get_contents( $cache_file );
    $message = substr_replace(
            $message,$html,$pos,strlen($tex_matches[0][$i]));
}

?>
