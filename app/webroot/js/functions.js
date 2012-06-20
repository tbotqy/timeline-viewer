function scrollToPageTop(e){
   e.preventDefault();
  
  $("html, body").animate(
    {scrollTop:0},
    {easing:"swing",duration:500}
  );
  
  return false;
}

function scrollDownToDestination(e,distance){
  e.preventDefault();
  distance -= 160;
  $("html, body").animate(
    {scrollTop: distance},
    {easing:"swing",duration:500}
  );

  return false;
}