var xhrCount=0;

window.addEventListener('load', init, false )


function init(){
  
        $('body').on('keyup','.w4SearchBar',function(e){
    	
        
        
        
        
        if(e.keyCode!=38&&e.keyCode!=40){
		var par = $(this).closest('.w4SearchArea');
		if($(this).val().length>=1){
		
			$('.searchResults',par).removeClass('active');
			var _searchQuery = $(this).val();
            
            var id = par.attr('id');
        
            var what = par;
            
            
				searchCall(what,_searchQuery);
			$(this).addClass('loading');
		
			
		}else{
			$('.searchResults').removeClass('active');
		}
    }else{
      
   //whatInput = $(e.currentTarget).closest('.innerS');
   //  navThroughLi(e);
   // e.preventDefault();
  // return false;
    }
	});
    
    $('.w4SearchArea').mouseleave(function(e){
        
        $('.searchResults',this).removeClass('active');
    })
    
};



function searchCall(what,_searchQuery){
       
		 var seqNumber = ++xhrCount;
		
			 $.ajax({
                type       : "GET",
                data       : {action:"w4_sas_search",searchQuery:_searchQuery,
							  
							
							 
							 },
                dataType   : "html",
                url        : myAjax.ajaxurl,
                beforeSend : function(){
                },
                success    : function(data){
					
 if (seqNumber === xhrCount) {
            //console.log(xhrCount+"Received XHR #" + seqNumber + ", Process the response");
     
     $('.w4SearchBar',what).removeClass('loading');
					
					$('.searchResults ul',what).html('');
					$('.searchResults ul',what).append(data);
					$('.searchResults',what).addClass('active');
        } else {
            //console.log("Received XHR #" + seqNumber + ", Ignore the response");
        }
			
                },
                error     : function(jqXHR, textStatus, errorThrown) {
                    //alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					loading=false;
                }
        });
	}
