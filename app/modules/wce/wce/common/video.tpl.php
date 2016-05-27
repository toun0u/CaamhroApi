<?php
$pathinfo = pathinfo($this->getwebpath());
?>
<html>
    <head>
        <script src="/js/jquery-1.7.1.min.js"></script>
        <!-- Intégration de MediaElement.js -->
        <script src="/js/mediaElement/mediaelement-and-player.min.js"></script>
        <link rel="stylesheet" href="/js/mediaElement/mediaelementplayerGREEN.css" media="screen">
        <style type="text/css">
        	body {
        		margin: 0px;
                overflow : none;
        	}
        </style>
        <script type="text/javascript">
        $(document).ready(function() {
            var ie = (document.all && !window.opera)?true:false;
            if(ie){
                $('audio,video').mediaelementplayer({
                    alwaysShowControls: true,
                    videoVolume: 'horizontal',
                    features: ['progress','playpause'],
                    plugins: ['flash','silverlight'],
                    success: function (mediaElement) {
                         parent.styleFunction(mediaElement, "<?php echo $this->fields['tpl']; ?>", "<?php echo $this->fields['color']; ?>");
                    }
                });
            }
            else{
                $('audio,video').mediaelementplayer({
                    alwaysShowControls: true,
                    videoVolume: 'horizontal',
                    features: ['progress'],
                    plugins: ['flash','silverlight'],
                    success: function (mediaElement) {
                         parent.styleFunction(mediaElement, "<?php echo $this->fields['tpl']; ?>", "<?php echo $this->fields['color']; ?>");
                    }
                });
            }
        });
        </script>
    </head>
    <body>
        <video preload="none" width="<?php echo $this->fields['width']; ?>" height="<?php echo $this->fields['height']; ?>" poster="<?php echo $pathinfo['dirname']; ?>/_preview_<?php echo $pathinfo['filename']; ?>.jpg" src="">
            <!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
            <source type="video/mp4" src="<?php echo $pathinfo['dirname']; ?>/_preview_<?php echo $pathinfo['filename']; ?>.mp4" />

            <!-- WebM/VP8 for Firefox4, Opera, and Chrome -->
            <source type="video/webm" src="<?php echo $pathinfo['dirname']; ?>/_preview_<?php echo $pathinfo['filename']; ?>.webm" />

            <!-- Flash fallback for non-HTML5 browsers without JavaScript -->
            <object width="485" height="273" type="application/x-shockwave-flash" data="/js/mediaElement/flashmediaelement.swf">
                <param name="movie" value="/js/mediaElement/flashmediaelement.swf" />
                <param name="flashvars" value="controls=true&file=<?php echo $pathinfo['dirname']; ?>/_preview_<?php echo $pathinfo['filename']; ?>.mp4" />

                <!-- Image as a last resort -->
                <img src="/js/mediaElement/videoError.png" width="485" height="273" title="Impossible de lire la vidéo" />
            </object>
        </video>
    </body>
</html>