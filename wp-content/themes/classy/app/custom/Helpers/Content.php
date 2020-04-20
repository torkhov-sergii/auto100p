<?php namespace Helpers;

use Carbon\Carbon;

class Content
{

  /**
   * Returns gallery HTML
   *
   * @return string
   */
  public static function galleryHTML($images)
  {
    $html = "<div class='gallery m_".count($images)."'>";

    if(count($images) < 5) {
      $html .= "<div class='gallery__column m_".count($images)."'>";
      foreach($images as $image_link) {
        $html .= "<img class='gallery__image b-lazy' src='".$image_link."'>";
      }
      $html .= "</div>";
    } else {
      $html .= "<div class='gallery__column m_4'>";
      for ($k = 0 ; $k < 4; $k++) {
        $html .= "<img class='gallery__image b-lazy' src='".$images[$k]."'>";
      }
      $html .= "</div>";
      $html .= "<div class='gallery__column m_".(count($images) - 4)."'>";
      for ($k = 4 ; $k < count($images); $k++) {
        $html .= "<img class='gallery__image b-lazy' src='".$images[$k]."'>";
      }
      $html .= "</div>";
    }
    $html .= "</div>";

    return $html;
  }

  /**
   * Returns video HTML
   *
   * @return string
   */
  public static function videoHTML($video)
  {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['acf_video'], $match);
    $thumbnail = $video['acf_video_thumbnail']['sizes']['large'];
    $html = "<div class='video'>";
    $html .= "<div class='video__inner' data-src='".array_pop($match)."' data-preview='".$thumbnail."'></div>";
    $html .= "<div class='video__title'>".$video['acf_video_title']."</div>";
    $html .= "</div>";
    return $html;
  }
}