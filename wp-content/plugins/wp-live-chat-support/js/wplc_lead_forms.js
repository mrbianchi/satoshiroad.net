jQuery("#nimble_lead_form").change(function() { 
  jQuery("#nimble_survey").prop('selectedIndex',0);
});

jQuery("#nimble_survey").change(function() { 
  jQuery("#nimble_lead_form").prop('selectedIndex',0);
});