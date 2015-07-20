<script id="church-summary" type="text/x-handlebars-template">
  <li class="list-group-item">
    <h4 class="list-group-item-heading" id="list-group-item-heading"><a target="_blank" href="{{ url }}">{{{ name }}}</a> <span class="badge">{{ region.denomination.tag_name }}</span></a></h4>
    <p class="list-group-item-text">{{ address}}</p>
    <p class="list-group-item-text">{{ city }}, {{ state }} {{ zip}}<br></p>
    <p class="list-group-item-text">Leader: {{ leader }}</p>
    <p class="list-group-item-text">{{ phone}}</p>
    <p class="list-group-item-text"><em>{{ distance_in_miles }} miles away</em></p>
    <p><a target="_blank" href="http://maps.google.com/maps?f=q&amp;hl=en&amp;saddr={{ latitude }},{{ longitude }}&amp;daddr={{ latitude }},{{longitude }}">Driving directions</a>
    <p></p>
  </li>
</script>