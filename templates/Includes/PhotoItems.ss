<% if PaginatedPhotos %>
<ul id="album-photos">
    <% loop PaginatedPhotos %>
    <li class="album-photo">
        <% if $Type == "Photo" %>
        <a href="$PhotoSized(800,800).URL" rel="shadowbox[Gallery]" title="$Caption"  style="$ThumbStyle(125,125)">
            <img src="$PhotoCropped(125,125).URL" alt="$Caption" />
            <span></span>
        </a>
        <% else_if $Type == "Video" %>
        <a href="$VideoItem.GetEmbedUrl(800,800,1)" rel="shadowbox[Gallery];$VideoRel(800,800)" title="$Caption" style="$ThumbStyle(125,125)">
            <img src="$VideoItem.ThumbURL" alt="$Caption" style="width: 100%; height: auto; max-width: 100%; " />
            <span></span>
        </a>
        <% end_if %>
    </li>
    <% end_loop %>
</ul>
<% if PaginatedPhotos.MoreThanOnePage %>
<div class="pagination">
    <div class="page-numbers">
        <ul>
            <% if PaginatedPhotos.NotFirstPage %>
            <li class="previous"><a title="<% _t('VIEWPREVIOUSPAGE','View the previous page') %>" href="$PaginatedPhotos.PrevLink"><% _t('PREVIOUS','&larr;') %></a></li>
            <% else %>
            <li class="previous-off"><% _t('PREVIOUS','&larr;') %></li>
            <% end_if %>
            <% loop PaginatedPhotos.Pages %>
            <% if CurrentBool %>
            <li class="active">$PageNum</li>
            <% else %>
            <li><a href="$Link" title="<% sprintf(_t('VIEWPAGENUMBER','View page number %s'),$PageNum) %>">$PageNum</a></li>
            <% end_if %>
            <% end_loop %>
            <% if PaginatedPhotos.NotLastPage %>
            <li class="next"><a title="<% _t('VIEWNEXTPAGE', 'View the next page') %>" href="$PaginatedPhotos.NextLink"><% _t('NEXT','&rarr;') %></a></li>
            <% else %>
            <li class="next-off"><% _t('NEXT','&rarr;') %> </li>
            <% end_if %>
        </ul>
    </div><!-- page-numbers -->
</div><!-- .pagination -->
<% end_if %>
<% else %>
<p>There are no photos in this album.</p>
<% end_if %>