<?php

# 这里可以具体的去覆盖一些权限的配置
# 请参照文档进行相关的配置


# 仅注册用户或本地请求可以对文章进行修改阅读
$wgGroupPermissions['*']['read'] = false;
if ( !isset( $_SERVER['REMOTE_ADDR'] ) OR $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
    $wgGroupPermissions['*']['read'] = true;
    $wgGroupPermissions['*']['edit'] = true;
}