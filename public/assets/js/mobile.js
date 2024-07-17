function checkPageSize() {
    const width = window.innerWidth;
    const height = window.innerHeight;
    const isMobile = width <= 500 || height <= 500;
  
    document.body.classList.remove('mobile', 'desktop');
  
    if (isMobile) {
      document.body.classList.add('mobile');
    } else {
      document.body.classList.add('desktop');
    }
  }
  
  // Call the function on page load
  checkPageSize();
  
  // Add an event listener to call the function again on resize
  window.addEventListener('resize', checkPageSize);
  