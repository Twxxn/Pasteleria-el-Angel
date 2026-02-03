document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
  
    let index = 0;
  
    function showSlide(i) {
      slides.forEach(slide => slide.style.display = 'none');
      slides[i].style.display = 'block';
    }
  
    function autoSlide() {
      index = (index + 1) % slides.length;
      showSlide(index);
    }
  
    showSlide(index);
    let interval = setInterval(autoSlide, 4000);

    function showSlide(i) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[i].classList.add('active');
      }
      
  
    nextBtn.addEventListener('click', () => {
      clearInterval(interval);
      index = (index + 1) % slides.length;
      showSlide(index);
      interval = setInterval(autoSlide, 4000);
    });
  
    prevBtn.addEventListener('click', () => {
      clearInterval(interval);
      index = (index - 1 + slides.length) % slides.length;
      showSlide(index);
      interval = setInterval(autoSlide, 4000);
    });
  });
  