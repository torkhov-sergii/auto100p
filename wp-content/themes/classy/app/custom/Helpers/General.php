<?php namespace Helpers;

use Carbon\Carbon;
include ('Content.php');

class General
{

    public static $resourceActionNames = [
        'events' => 'Sign up',
        'webinars' => 'Sign up',
        'podcasts' => 'Listen',
        'articles' => 'Read',
        'discussion' => 'View discussion'
    ];

    /**
     * Returns formatted post content with replaced ACF tags to their HTML
     *
     * @return string
     */
    public static function getContentWithAcf($post)
    {
      $content = $post->content();
      $repeaters = [
        'acf_galleries' => [],
        'acf_videos' => []
      ];

    // Iterate through repeaters to get ACF fields content
    foreach(array_keys($repeaters) as $field) {
        if($post->getAcfByKey($field) !== null && $post->getAcfByKey($field) !== false) {
            foreach($post->getAcfByKey($field) as $index => $item) {
                    switch ($field) {
                        case "acf_galleries":
                        array_push($repeaters[$field], array_column($item['acf_gallery'], 'url'));
                        break;
                        case "acf_videos":
                        array_push($repeaters[$field], $item);
                        break;
                    }
            }
        }
    }

    // Insert new items into WP Post content
    foreach ($repeaters as $key => $content_to_insert) {
        if (!empty($content_to_insert)) {
            foreach ($content_to_insert as $number => $item) {
                if(strpos($content, '[GALLERY_'.$number.']') !== FALSE && $key == 'acf_galleries') {
                    $content = str_replace('[GALLERY_'.$number.']', Content::galleryHTML($item), $content);
                }
                if(strpos($content, '[VIDEO_'.$number.']') !== FALSE && $key == 'acf_videos') {
                    $content = str_replace('[VIDEO_'.$number.']', Content::videoHTML($item), $content);
                }
            }
        }
    }

    return $content;
    }

    /**
     * Returns formatted date
     *
     * @return string
     */
    public static function getFormattedDate($date, $format = 'd/m/y')
    {
        if ($format == 'for_humans') return Carbon::parse($date)->diffForHumans();

        return $date ? $str = ltrim(Carbon::parse($date)->format($format), '0') : null;
    }

    /**
     * Returns truncated string with '...' ellipsis
     *
     * @return string
     */
    public static function getTruncatedString($string, $length = 100, $append = "&hellip;")
    {
        $string = self::fixFigureTag($string);
        $string = self::fixDiscussionSymbol($string);
        $string = self::fixTextIssues($string);

        $string = strip_tags(trim($string));

        return preg_replace('/\s+?(\S+)?$/', $append, substr($string, 0, $length));

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = self::fixLikesCounter($string);
            $string = $string[0] . $append;
        }
        return $string;
    }

    // Remove weird symbols from discussions
    public static function fixDiscussionSymbol($string)
    {
        $string = str_replace('┬á', ' ', $string);
        $string = str_replace('ΓÇï', '', $string);
        return $string;
    }

    // Transform text to look nicely
    public static function fixTextIssues($string)
    {
        $string = html_entity_decode($string);
        return $string;
    }

    // Remove weird <figure> tag from old psf.net articles
    public static function fixFigureTag($string)
    {
        $string = preg_replace('#(<figure.*?>).*?(</figure>)#', '', $string);
        return $string;
    }

    //file with hash (updated)
    public static function asset_hash($file) {

        if(file_exists('.'.$file)) {
            $hash = hash('crc32', filemtime('.'.$file));
            return ($file.'?'.$hash);
        }

        return $file;
    }

    public static function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}