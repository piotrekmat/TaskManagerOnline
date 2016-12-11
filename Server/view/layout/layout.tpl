{$this->doctype()}
<html lang="pl">
    <head>
        <meta charset="utf-8">
        {$this->headTitle()->setSeparator(' - ')->setAutoEscape(false)}

        {$this->headMeta()}
        {$basePath = $this->basePath()}

        <!--  Styles -->
        {$this->headLink([ 'rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' =>"`$basePath`/images/favicon.ico"])}
        {$this->headLink()
            ->appendStylesheet("`$basePath`/css/bootstrap.css")
            ->appendStylesheet("`$basePath`/css/bootstrap-theme.css")
            ->appendStylesheet("`$basePath`/css/bootstrap-responsive.min.css")}

        <!-- Le styles -->
        {$this->headLink()}

        {$this->headScript()->appendFile("`$basePath`/js/html5.js", "text/javascript", ['conditional' => 'lt IE9'])}
        {$this->headScript()->appendFile("`$basePath`/js/jquery-1.10.2.js", "text/javascript")}
        {$this->headScript()->appendFile("`$basePath`/js/bootstrap.min.js", "text/javascript")}
        {$this->headScript()->appendFile("`$basePath`/js/bootstrap.js", "text/javascript")}

        <!-- Scripts -->
        {$this->headScript()}

    </head>
    <body>
        {* <nav class="navbar navbar-default "></nav> *}
        <nav class="navbar navbar-inverse {* navbar-fixed-top *}" role="navigation">
            <div class="container-full">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="#">
                        <span class="btn-lg glyphicon glyphicon-menu-hamburger white"></span>
                    </a>

<!-- <a class="navbar-brand" href="{$this->url('home')}"><img src="{$this->basePath('img/zf2-logo.png')}" alt="Zend Framework 2"/>{$this->translate('Skeleton Application')}</a> -->
                </div>
                <div class="collapse navbar-collapse">
                   GENEROWANIE RAPORTU
                </div>
            </div>
        </nav>
        <div class="">
            {include file='../info/info.tpl'}
            <div class="container">
                <div class="col-lg-3">
                    <nav class="nav-stacked bs-docs-sidebar hidden-print hidden-xs hidden-sm">
                    </nav>
                </div>
                <div class="col-lg-9">
                    {*<div>Navi: {$this->navigation('navigation')->breadcrumbs()->setMaxDepth(9)}</div>*}

                    {$this->content}

                </div>
                <div class="clearfix"></div>
                <hr>
                <footer>

                    <p>&copy; 2006 - 2012 by Zend Technologies Ltd. All rights reserved.</p>
                </footer>

            </div>
        </div>
        <!-- /container -->

    </body>
</html>
