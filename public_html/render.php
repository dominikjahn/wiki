<?php
    header("Content-Type","application/json");

    $page = json_decode($_POST["data"]);
    $rendered = $page->page->content;
    /*
     * NoParse
     */

    # <Wiki:NoParse>Content</Wiki:NoParse>
    $blocks = [];
    $noparse = [];
    preg_match_all("/<Wiki:NoParse>(?<content>.+?)<\/Wiki:NoParse>/muis",$rendered,$blocks, PREG_SET_ORDER);

    foreach($blocks as $block)
    {
        $wrapper = $block[0];
        $content = $block["content"];

        $blockID = md5($content.microtime(true));

        $noparse[$blockID] = $content;

        $rendered = str_replace($wrapper, '<!-- NOPARSE:'.$blockID.' -->', $rendered);
    }

    /* Exclude <script> and <style> */
    $blocks = [];
    $nomarkdown = [];
    preg_match_all("/<(script|style|Wiki:NoMarkdown).*?>.+?<\/\\1>/muis",$rendered,$blocks, PREG_SET_ORDER);

    foreach($blocks as $block)
    {
        $wrapper = $block[0];

        $blockID = md5($wrapper.microtime(true));

        $nomarkdown[$blockID] = $wrapper;

        $rendered = str_replace($wrapper, '<!-- NOMARKDOWN:'.$blockID.' -->', $rendered);
    }

    /*
     * Icons
     */

    # <Wiki:Icon name="Icon"/>
    $icons = array();
    preg_match_all("/<Wiki:Icon\s*name=['\"](?<name>[a-zA-Z0-9\-]+)['\"]\s*\/>/muis",$rendered,$icons, PREG_SET_ORDER);

    foreach($icons as $icon)
    {
        $wrapper = $icon[0];
        $name = $icon["name"];

        $rendered = str_replace($wrapper, '<span class="glyphicon glyphicon-'.$name.'" aria-hidden="true"></span>', $rendered);
    }

    /*
     * Panels
    */

    # Basic panel <Wiki:Panel>Content</Wiki:Panel>
    $panels = array();
    preg_match_all("/<Wiki:Panel\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$rendered,$panels, PREG_SET_ORDER);

    foreach($panels as $panel)
    {
        $wrapper = $panel[0];
        $content = $panel["content"];

        $panel = '<div class="panel panel-default"><div class="panel-body">'.$content.'</div></div>';

        $rendered = str_replace($wrapper, $panel, $rendered);
    }

    # Basic panel with title: <Wiki:Panel title="Title">Content</Wiki:Panel>
    $panels = array();
    preg_match_all("/<Wiki:Panel\s*title=['\"](?<title>.+?)['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$rendered,$panels, PREG_SET_ORDER);

    foreach($panels as $panel)
    {
        $wrapper = $panel[0];
        $content = $panel["content"];
        $title = $panel["title"];

        $panel = '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div><div class="panel-body">'.$content.'</div></div>';

        $rendered = str_replace($wrapper, $panel, $rendered);
    }

    # Basic panel with theme: <Wiki:Panel theme="theme">Content</Wiki:Panel>
    $panels = array();
    preg_match_all("/<Wiki:Panel\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$rendered,$panels, PREG_SET_ORDER);

    foreach($panels as $panel)
    {
        $wrapper = $panel[0];
        $content = $panel["content"];
        $theme = $panel["theme"];

        $panel = '<div class="panel panel-'.$theme.'"><div class="panel-body">'.$content.'</div></div>';

        $rendered = str_replace($wrapper, $panel, $rendered);
    }

    # Basic panel with title and theme: <Wiki:Panel theme="theme" title="Title">Content</Wiki:Panel>
    $panels = array();
    preg_match_all("/<Wiki:Panel\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*title=['\"](?<title>.+?)['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$rendered,$panels, PREG_SET_ORDER);

    foreach($panels as $panel)
    {
        $wrapper = $panel[0];
        $content = $panel["content"];
        $title = $panel["title"];
        $theme = $panel["theme"];

        $panel = '<div class="panel panel-'.$theme.'"><div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div><div class="panel-body">'.$content.'</div></div>';

        $rendered = str_replace($wrapper, $panel, $rendered);
    }

    /*
     * Alerts
    */

    # <Wiki:Alert theme="theme">Content</Wiki:Alert>
    $alerts = array();
    preg_match_all("/<Wiki:Alert\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Alert>/muis",$rendered,$alerts, PREG_SET_ORDER);

    foreach($alerts as $alert)
    {
        $wrapper = $alert[0];
        $content = $alert["content"];
        $theme = $alert["theme"];

        $alert = '<div class="alert alert-'.$theme.'" role="alert">'.$content.'</div>';

        $rendered = str_replace($wrapper, $alert, $rendered);
    }

    /*
     * Labels
    */
    # <Wiki:Label theme="theme">Content</Wiki:Label>
    $labels = array();
    preg_match_all("/<Wiki:Label\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Label>/muis",$rendered,$labels, PREG_SET_ORDER);

    foreach($labels as $label)
    {
        $wrapper = $label[0];
        $content = $label["content"];
        $theme = $label["theme"];

        $label = '<div class="label label-'.$theme.'">'.$content.'</div>';

        $rendered = str_replace($wrapper, $label, $rendered);
    }

    /*
     * Re-insert <Wiki:NoParse>
     */

    foreach($noparse as $blockID => $content) {
        $rendered = str_replace('<!-- NOPARSE:'.$blockID.' -->', $noparse[$blockID], $rendered);
    }

    /*
     * Re-insert <script>/<style>
     */

    foreach($nomarkdown as $blockID => $block) {
        $rendered = str_replace('<!-- NOMARKDOWN:'.$blockID.' -->', $block, $rendered);
    }

    $page->page->content = $rendered;

    print json_encode($page);
?>