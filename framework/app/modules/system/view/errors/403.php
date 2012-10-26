<div class="box" style="height: 100%;">
    <h2>Error {{ERR_CODE}}:{{ERR_TITLE}}</h2>
        <p>{{ERR_DESCRIPTION}}</p>
    <br/>
    <h4><?php print _("Common causes of this error:"); ?></h4>
    <ol>
        {{ERR_CAUSES}}
    </ol>
    <br />
    <h4><?php print _("Actions");?></h4>
    <ol>
        {{ERR_ACTIONS}}
    </ol>
</div>