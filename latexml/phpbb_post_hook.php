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
// include this file in includes/functions_posting.php
// and call clear_latexml_cache($uid) on 
//      line 1637 (submit_post) right after the code to bail if action is delete 
//      line 1394 (delete_post) at the very start of deleting the post 

// --------------------------------------------------------------------------------------------------
// adjust this to match your system configuration
function clear_latexml_cache($uid)
{
    $latexmlcache_path = "/var/www/phpBB3/latexml/cache";

    // --------------------------------------------------------------------------------------------------
    $i=0;
    do 
    {
        $cache_file = sprintf("%s/%s.%d",$latexmlcache_path,$uid,$i);
        if( file_exists($cache_file) )
        {
            unlink($cache_file);
            $i++;
        }
        else
            break;
    }while(true);
}
