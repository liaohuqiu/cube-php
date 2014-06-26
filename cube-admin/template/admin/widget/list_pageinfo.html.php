<div class='ui_page'>
    <?php $nav = $page_data['pagination']; ?>
        <select class="__j_pageinfo_num_perpage span1">
            <?php $page_data['pagination']->o('numPerPageOptions', false);?>
        </select>items per page;
        total pages: <?php $page_data['pagination']->o('totalPage'); ?>; total items:<?php $page_data['pagination']->o('total'); ?></span>
    <ul class="pagination">
        <?php if ($nav['head_url']): ?>
        <li><a href="<?php $nav->o('head_url'); ?>">First</a></li>
        <?php else: ?>
        <li class='disable'><a href="#">First</a></li>
        <?php endif; ?>
        <?php if ($nav['prev_url']): ?>
        <li><a href="<?php $nav->o('prev_url'); ?>">&laquo;</a></li>
        <?php else: ?>
        <li class='disable'><a href="#">&laquo;</a></li>
        <?php endif; ?>
        <?php foreach ($nav['pages'] as $page): ?>
            <?php if ($page['is_current']): ?>
            <li class='active'><a href="<?php $page->o('url'); ?>"><?php $page->o('page'); ?></a></li>
            <?php else: ?>
            <li><a href="<?php $page->o('url'); ?>"><?php $page->o('page'); ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($nav['next_url']): ?>
        <li><a href="<?php $nav->o('next_url'); ?>">&raquo;</a></li>
        <?php else: ?>
        <li class='disable'><a href="#">&raquo;</a></li>
        <?php endif; ?>
        <?php if ($nav['tail_url']): ?>
        <li><a href="<?php $nav->o('tail_url'); ?>">Last</a></li>
        <?php else: ?>
        <li class='disable'><a href="#">Last</a></li>
        <?php endif; ?>
    </ul>
</div>
