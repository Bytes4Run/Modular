{{if isset($data.layout.head)}}
    {{assign var="head" value=$data.layout.head}}
{{/if}}
{{if isset($data.layout.footer)}}
    {{assign var="footer" value=$data.layout.footer}}
{{/if}}
{{if isset($data.layout.body)}}
    {{assign var="body" value=$data.layout.body}}
{{/if}}
{{if isset($data.layout.scripts)}}
    {{assign var="scripts" value=$data.layout.scripts}}
{{/if}}
{{if isset($data.layout.header)}}
    {{assign var="header" value=$data.layout.header}}
{{/if}}
{{if isset($data.layout.menu)}}
    {{assign var="menu" value=$data.layout.menu}}
{{/if}}
{{if isset($data.content)}}
    {{assign var="content" value=$data.content}}
{{/if}}
{{if isset($data.layout.app)}}
    {{assign var="app" value=$data.layout.app}}
{{/if}}
{{* To load any layout without assigning variables *}}
<!DOCTYPE html>
{{block name="layout"}}<html lang="en"><head><meta charset="UTF-8"><title>APP</title></head><body></body></html>{{/block}}