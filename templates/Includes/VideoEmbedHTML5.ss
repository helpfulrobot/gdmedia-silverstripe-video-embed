<div class='media $ExtraClass'>
    <video id="vid1" class="video-js vjs-default-skin" controls preload="auto" width="$Width" height="$Height" style="width: {$Width}px; height: {$Height}px" data-setup='$SetupData' poster='$ThumbURL' <% if $Autoplay %>autoplay<% end_if %>>
           <% if $HTML5Video.Exists %>
           <source src="$HTML5Video.GetURL" type="$MimeType">
            <% end_if %>
            Your browser does not support the video tag.
    </video>
</div>