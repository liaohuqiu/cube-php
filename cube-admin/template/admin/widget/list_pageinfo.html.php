<div class='ui_page'>
    <?php if $pageData.pagination.prevPageUrl ?> <a href="<?php $pageData.pagination.prevPageUrl ?>">prev</a> <?php else: ?><a>prev</a>
    <?php endif; ?>| <?php if $pageData.pagination.nextPageUrl ?> <a href="<?php $pageData.pagination.nextPageUrl ?>">next</a>
    <?php else: ?><a>next</a><?php endif; ?> <span>page: <?php $pageData.pagination.currentPage ?>
        <select class="__j_pageinfo_num_perpage span1">
            <?php $pageData.pagination.numPerPageOptions nofilter ?>
        </select>items per page;
        total pages: <?php $pageData.pagination.totalPage ?>; total items:<?php $pageData.pagination.total ?></span>
</div>
