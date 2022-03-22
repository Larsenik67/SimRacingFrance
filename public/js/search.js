/*function showResult(str) {
    if (str.length==0) {
      document.getElementById("livesearch").innerHTML="";
      document.getElementById("livesearch").style.border="0px";
      return;
    }
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
      if (this.readyState==4 && this.status==200) {
        document.getElementById("livesearch").innerHTML=this.responseText;
        document.getElementById("livesearch").style.border="1px solid #A5ACB2";
      }
    }
    xmlhttp.open("GET","livesearch.php?q="+str,true);
    xmlhttp.send();
  }*/

  $(document).ready(function(nom){
      $('#search_bar_search').keyup(function(){
          var text = $(this).val()
          $.ajax({
              type: 'GET',
              url: "{{ path('app_search_ajax') }}",
              data: text,
              success: function(data) {
                console.log(data)
            }
          })
      });
  });