<script>
    $('#modal_editar_usuario').on('show.bs.modal', function(e) {
      //  var id = $(e.relatedTarget).data().equipo;
      // $(e.currentTarget).find('#equipo').val(id);
      var id = $(e.relatedTarget).data().id_usuario;
      $(e.currentTarget).find('#id_usuario').val(id);
      var id = $(e.relatedTarget).data().name;
      $(e.currentTarget).find('#name').val(id);

      var id = $(e.relatedTarget).data().email;
      $(e.currentTarget).find('#email').val(id);
     //


      var id = $(e.relatedTarget).data().rol_id;
      $(e.currentTarget).find('.rol_id').val(id);

      var id = $(e.relatedTarget).data().plan_id;
      $(e.currentTarget).find('.plan_id').val(id);

  });

new DataTable('#tblusuario', '#tblsmsgateway');


</script>
<script>
  $(document).ready(function () {
      var sidebar = $(".sidebar");
      var canvas = $(".offcanvasRight");
      var isMouseOver = false;

      sidebar.mouseenter(function () {
          isMouseOver = true;
          showCanvas();
      });

      sidebar.mouseleave(function () {
          isMouseOver = false;
          hideCanvas();
      });

      $(document).mousemove(function (e) {
          if (isMouseOver && e.pageX > sidebar.width()) {
              showCanvas();
          } else {
              hideCanvas();
          }
      });

      function showCanvas() {
          canvas.addClass("visible");
      }

      function hideCanvas() {
          canvas.removeClass("visible");
      }
  });
  </script>
