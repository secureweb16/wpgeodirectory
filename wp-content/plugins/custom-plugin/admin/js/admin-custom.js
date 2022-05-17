
/* Pagination */



jQuery(document).ready(function ($) {


  jQuery('.prev').hide();
  pageSize = 10;
  var oldpage = '';
  
  var pageCount =  jQuery(".page-pagination").length / pageSize;  
  pageCount = Math.ceil(pageCount);

  if(pageCount == 0 || pageCount == 1){
    $('.pagination').hide();
  }
  
  for(var i = 0 ; i<pageCount;i++){
    if( i == 1 ){
      $("#pagin").append('<span class="prevdotclass" style="display:none;">...</span> ');
    }
    if(i <=1){
      jQuery("#pagin").append('<li id="idsactive_'+(i+1)+'"><a href="javascript:void(0);">'+(i+1)+'</a></li> ');
    }
    else{
      if(pageCount == i+1){
        jQuery("#pagin").append('<li class="dotclass">...</li> ');
        jQuery("#pagin").append('<li id="idsactive_'+(i+1)+'"><a href="javascript:void(0)">'+(i+1)+'</a></li> ');
      }else{
        jQuery("#pagin").append('<li id="idsactive_'+(i+1)+'" style="display:none;"><a href="javascript:void(0)">'+(i+1)+'</a></li> ');
      }
      
    }
  }

  jQuery("#pagin li").first().find("a").addClass("active")
  
  showPage = function(page) {

    var newpage = page+1;
    var prevpage = page-1;
    
    if(page > 1){ jQuery('.prev').show(); }else { jQuery('.prev').hide(); }
    if(page == pageCount){ jQuery('.next').hide(); }else { jQuery('.next').show(); }    
    if(page >=2 ){ jQuery('#idsactive_'+newpage).show(); }
    if(page == pageCount || pageCount == page+1 ){ jQuery('.dotclass').hide(); } else { jQuery('.dotclass').show();} 
    
    
    if(page > 1 && page > oldpage ){      
      $('.prevdotclass').show();
      $('[id^=idsactive_]').hide();
      $('#idsactive_1').show();
      $('#idsactive_'+page).show();
      $('#idsactive_'+newpage).show();
      $('#idsactive_'+pageCount).show();
      
    }
    else if(page == oldpage || oldpage >= page){      
      $('.prevdotclass').show();      
      $('[id^=idsactive_]').hide();
      $('#idsactive_1').show();
      $('#idsactive_'+page).show();
      $('#idsactive_'+prevpage).show();
      $('#idsactive_'+pageCount).show();

    }

    if(page== 1 || page== 2) {
      $('.prevdotclass').hide();  
      $('#idsactive_2').show();
    }
    if(page == pageCount){
      $('#idsactive_'+prevpage).show();
    }

    var licount =  jQuery("#pagin li[id^=idsactive_]").length;      
    if(page == 3){      
      var attrvaue = $('#idsactive_2').attr('style');            
      if(attrvaue == '' || typeof attrvaue === 'undefined'){
        $('.prevdotclass').hide();
      }
    }

    if(page == licount-2 ){      
      var attrvaue = $('#idsactive_'+(licount-1)).attr('style');            
      if(attrvaue == '' || typeof attrvaue === 'undefined' ){
        $('.dotclass').hide();
      }
    }

    oldpage = page; 

    jQuery(".page-pagination").hide();
    jQuery(".page-pagination").each(function(n) {
      if (n >= pageSize * (page - 1) && n < pageSize * page)
        jQuery(this).show();
    });        
  }

  showPage(1);

  jQuery("#pagin li a").click(function() {
    jQuery("#pagin li a").removeClass("active");
    jQuery(this).addClass("active");
    showPage(parseInt(jQuery(this).text())) 

  });
  
  
  jQuery('body').on('click', '.next', function() {

    var page = jQuery("#pagin li").find('.active').text();  
    page = parseInt(page, 10);
    var finalpage = page+1;
    showPage(finalpage)
    jQuery("#pagin li a").removeClass("active");
    jQuery("#idsactive_"+finalpage).find('a').addClass("active");   
  });
  
  
  jQuery('body').on('click', '.prev', function() {  
    var page = jQuery("#pagin li").find('.active').text();  
    page = parseInt(page, 10);
    var finalpage = page-1;
    showPage(finalpage)
    jQuery("#pagin li a").removeClass("active");
    jQuery("#idsactive_"+finalpage).find('a').addClass("active");   
  });

  
    jQuery('.add-new').click(function(){       
      jQuery('.add-coin-promation').addClass('show');
      jQuery('body').addClass('show-popup');
      jQuery('.add-coin-promation').show();
    }); 
    jQuery('.add-coin-promation span.close').click(function(){
      jQuery('.add-coin-promation').removeClass('show');
      jQuery('body').removeClass('show-popup');
      jQuery('.add-coin-promation').hide();
    });
});

