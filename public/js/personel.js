function rtl() {
    var body = document.body;
    body.classList.toggle("rtl");
  }
  
  function dark() {
    var body = document.body;
    body.classList.toggle("dark");
  }
  
  
  
  
  $(document).ready(function () {
    $("ul.a-collapse").click(function () {
      // console.log($(this).hasClass("short"));
      if ($(this).hasClass("short")) {
        $(".a-collapse").addClass("short");
        $(this).toggleClass("short");
        $(".side-item-container").addClass("hide animated");
        $("div.side-item-container", this).toggleClass("hide animated");
      } else {
        $(this).toggleClass("short");
        $("div.side-item-container", this).toggleClass("hide animated");
      }
  
  
    });
  
  });
  
  
  
  
  
  
  var ctx = document.getElementById('myChart2');
  var ctx = new Chart(ctx, {
    type: 'polarArea',
    
    options: {
        
    }
  });
  
  
  
  
  var myChart4 = document.getElementById('myChart4');
  var myChart4 = new Chart(myChart4, {
    type: 'doughnut',
   
    options: {
       
    }
  });
  
  
  var mixChart = document.getElementById('myChart5');
  var mixedChart = new Chart(mixChart, {
    type: 'bar',
    
  
    options: {}
  });
  
  
  
  
  
  
  