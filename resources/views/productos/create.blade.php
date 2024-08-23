<!-- resources/views/productos/create.blade.php -->

@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>Crear Nuevo Producto</h1>

    <form id="create-product-form" enctype="multipart/form-data">
      @csrf
      <div class="row mb-20">
        <div class="col-md-6">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
              required>
          </div>
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="text" id="precio" name="precio"
              class="form-control">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad"
              class="form-control">
          </div>
          <div class="form-group">
            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-control">
              @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" accept="image/*" id="imagen" name="imagen"
              class="form-control">
          </div>
        </div>
      </div>
      <br>
      <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary">Crear Producto</button>
      </div>
    </form>
  </div>

  <script>
    document.getElementById('create-product-form').addEventListener('submit', function(
      event) {
      event.preventDefault();

      let producto = {
        nombre: document.getElementById('nombre').value,
        descripcion: document.getElementById('descripcion').value,
        precio: document.getElementById('precio').value,
        cantidad: document.getElementById('cantidad').value,
        categoria_id: document.getElementById('categoria_id').value,
      };

      let fileInput = document.getElementById('imagen');
      let file = fileInput.files[0];

      if (file) {
        let reader = new FileReader();
        reader.onloadend = function() {
          let base64String = reader.result;
          producto.imagen = base64String;

          enviarDatos(producto);
        }
        reader.readAsDataURL(file);
      } else {
        enviarDatos(producto);
      }
    });

    function enviarDatos(producto) {
      fetch('/api/productos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            Producto: producto
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.message === 'Producto creado con éxito') {
            document.getElementById('create-product-form')
          .reset(); // Limpiar el formulario
            alert('Producto creado con éxito');
          } else {
            alert('Error al crear el producto');
            console.error('Error:', data);
          }
        })
        .catch(error => {
          alert('Error al crear el producto');
          console.error('Error:', error);
        });
    }
  </script>
@endsection
