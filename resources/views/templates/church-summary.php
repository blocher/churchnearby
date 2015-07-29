<script id="church-summary" type="text/x-handlebars-template">
  <li class="list-group-item">
    <h4 class="list-group-item-heading" id="list-group-item-heading"><a target="_blank" href="{{ url }}">{{{ name }}}</a> <span class="badge">{{ region.denomination.tag_name }}</span></a></h4>
    <p class="external_id hidden">{{ external_id }}</p>
    <p class="internal_id hidden">{{ id }}</p>
    <p class="region_id hidden">{{ region_id }}</p>
    <p class="denomination_id hidden">{{ region.denomination.id }}</p>
    <p class="list-group-item-text">{{ region.long_name }}</p>
    <p class="list-group-item-text">{{ address}}</p>
    <p class="list-group-item-text">{{ city }}, {{ state }} {{ zip}}<br></p>
    <p class="list-group-item-text">Leader: {{ leader }}</p>
    <p class="list-group-item-text">{{ phone}}</p>
    <p class="list-group-item-text"><em>{{ distance_in_miles }} miles away</em></p>
    <p><a target="_blank" href="http://maps.google.com/maps?f=q&amp;hl=en&amp;saddr={{ latitude }},{{ longitude }}&amp;daddr={{ latitude }},{{longitude }}">Driving directions</a>
    <p></p>
  </li>
</script>