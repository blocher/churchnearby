<script id="church-listings" type="text/x-handlebars-template">
  <div class="list-group">
      <!-- <div class="alert alert-success"><h4>Your closest church is ... <a target="_blank" href="{{ churches.0.url }}"><strong>{{{ churches.0.name }}}</a></strong></h4></div> -->
      {{#if region}}
	   <div class="alert alert-info"><p>{{ region.message }}</p></div>
	  {{/if}}
      <h4>Closest churches to <strong><em>{{#if address}} {{ address }} {{else}} your current location{{/if}}</em></strong></h4>
       {{#each churches }}
        {{> church_summary }}
       {{/each }}
  </div>
</script>