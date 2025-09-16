<?php
/**
 * 分页助手函数
 */

/**
 * 生成分页链接
 * @param int $current_page 当前页码
 * @param int $total_pages 总页数
 * @param string $url 基础URL
 * @param array $get_params GET参数
 * @return string 分页HTML
 */
function paginate($current_page, $total_pages, $url, $get_params = []) {
    if ($total_pages <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation" class="mb-4"><ul class="pagination">';

    // 上一页
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . buildPageUrl($url, $current_page - 1, $get_params) . '">&laquo; Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }

    // 页码
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    if ($start_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . buildPageUrl($url, 1, $get_params) . '">1</a></li>';
        if ($start_page > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . buildPageUrl($url, $i, $get_params) . '">' . $i . '</a></li>';
        }
    }

    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . buildPageUrl($url, $total_pages, $get_params) . '">' . $total_pages . '</a></li>';
    }

    // 下一页
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . buildPageUrl($url, $current_page + 1, $get_params) . '">Next &raquo;</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}

/**
 * 构建分页URL
 * @param string $base_url 基础URL
 * @param int $page 页码
 * @param array $get_params GET参数
 * @return string 完整URL
 */
function buildPageUrl($base_url, $page, $get_params = []) {
    $params = $get_params;
    $params['page'] = $page;

    $query_string = http_build_query($params);

    return $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . $query_string;
}

/**
 * 获取当前页码
 * @param int $default 默认页码
 * @return int 当前页码
 */
function getCurrentPage($default = 1) {
    return isset($_GET['page']) ? max(1, intval($_GET['page'])) : $default;
}

/**
 * 计算分页信息
 * @param int $total_items 总项目数
 * @param int $current_page 当前页码
 * @param int $per_page 每页项目数
 * @return array 分页信息
 */
function calculatePagination($total_items, $current_page, $per_page = 10) {
    $total_pages = max(1, ceil($total_items / $per_page));
    $current_page = min($current_page, $total_pages);
    $offset = ($current_page - 1) * $per_page;

    return [
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'per_page' => $per_page,
        'offset' => $offset,
        'start_item' => $total_items > 0 ? $offset + 1 : 0,
        'end_item' => min($offset + $per_page, $total_items)
    ];
}

/**
 * 显示分页统计信息
 * @param array $pagination 分页信息
 * @return string HTML
 */
function showPaginationStats($pagination) {
    if ($pagination['total_items'] == 0) {
        return '<div class="text-center text-muted py-2">No items found</div>';
    }

    return '<div class="d-flex justify-content-between align-items-center mb-3 py-2">
        <div class="text-muted small">
            Showing ' . $pagination['start_item'] . ' to ' . $pagination['end_item'] . ' of ' . $pagination['total_items'] . ' entries
        </div>
        <div class="text-muted small">
            Page ' . $pagination['current_page'] . ' of ' . $pagination['total_pages'] . '
        </div>
    </div>';
}
?>