<script id="church-listings" type="text/x-handlebars-template">
  <div class="list-group">
      <div class="alert alert-success"><h4>Your closest church is ... <a target="_blank" href="{{ churches.0.url }}">{{ churches.0.name }}</a><strong></strong></h4></div>
      <!-- <div class="alert alert-info"><p>Your are likely in the </p></div> --> 
      <h4>Closest churches...</h4>
       {{#each churches }}
        {{> church_summary }}
       {{/each }}
  </div>
</script>