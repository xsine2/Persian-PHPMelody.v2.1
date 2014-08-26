{include file='header.tpl' p="article"} 
<div id="wrapper">
{if $show_addthis_widget == '1'}
{include file='widget-addthis.tpl'}
{/if}
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
            <h1>{$page.title}</h1>
			{$page.content}
		</div><!-- #primary -->
        </div><!-- #content -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
{include file='footer.tpl'}