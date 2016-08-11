$.ajax
({
  type: "GET",
  url: "index1.php",
  dataType: 'json',
  async: false,
  headers: {
    "Authorization": "Basic " + btoa(USERNAME + ":" + PASSWORD)
  },
  data: '{ "comment" }',
  success: function (){
    alert('Thanks for your comment!'); 
  }
});

