@extends('layouts.app')

@section('content')
  <div class="container">
    @if (isset($error))
      <div class="alert alert-danger">{{ $error }}</div>
    @else
      <div class="row">
        <div class="col-6">
          <h1>Productos</h1>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <button class="btn btn-primary" onclick="abrirModalCrear()">Nuevo
            producto</button>
        </div>
      </div>
      <div class="row mt-4">
        @foreach ($productos as $producto)
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <img
                src="{{ asset('storage/imagenes-productos/' . $producto['imagen']) }}"
                class="card-img-top img-fluid" alt="{{ $producto['nombre'] }}"
                style="object-fit: cover; height: 200px;">
              <div class="card-body">
                <h5 class="card-title">{{ $producto['nombre'] }}</h5>
                <p class="card-text">{{ $producto['descripcion'] }}</p>
                <p class="card-text"><strong>Precio:</strong>
                  ${{ $producto['precio'] }}</p>
                <p class="card-text"><strong>Cantidad:</strong>
                  {{ $producto['cantidad'] }}</p>
                <p class="card-text"><strong>Categoría:</strong>
                  {{ $producto['categoria_nombre'] }}</p>
              </div>
              <div class="card-footer d-flex justify-content-between">
                <button class="btn btn-warning"
                  onclick="editarProducto({{ $producto['id'] }})"><i
                    class="fas fa-edit"></i></button>

                <button onclick="deleteProducto({{ $producto['id'] }})"
                  class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="modal fade" id="productoModal" tabindex="-1" role="dialog"
    aria-labelledby="productoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Administrar producto</h5>
          <button type="button" class="btn-close" onclick="cerrarModal()"
            aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formularioProducto">
            @csrf
            <input type="hidden" id="productoId">
            <div class="form-group">
              <label for="nombreProducto">Nombre</label>
              <input type="text" id="nombreProducto" name="nombreProducto"
                class="form-control" required>
            </div>
            <div class="form-group">
              <label for="descripcionProducto">Descripción</label>
              <input type="text" id="descripcionProducto"
                name="descripcionProducto" class="form-control">
            </div>
            <div class="form-group">
              <label for="precioProducto">Precio</label>
              <input type="number" id="precioProducto" name="precioProducto"
                class="form-control">
            </div>
            <div class="form-group">
              <label for="cantidadProducto">Cantidad</label>
              <input type="number" id="cantidadProducto" name="cantidadProducto"
                class="form-control">
            </div>
            <div class="form-group">
              <label for="categoriaProducto">Categoría</label>
              <select id="categoriaProducto" name="categoriaProducto"
                class="form-control">
                @foreach ($categorias as $id => $nombre)
                  <option value="{{ $id }}">{{ $nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="imagenProducto">Imagen</label>
              <input type="file" id="imagenProducto" name="imagen"
                accept="image/*" class="form-control">
              <div class="d-flex justify-content-center">
                <small id="imagenActual"
                  class="text-center form-text text-danger"></small>
              </div>
            </div>
            <br>
            <div class="d-flex justify-content-center">
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script>
    function abrirModalCrear() {
      // Limpiar el formulario
      document.getElementById('formularioProducto').reset();
      document.getElementById('productoId').value = '';

      const modal = document.getElementById('productoModal');
      modal.style.display = 'block';
      modal.classList.add('show');
      modal.style.opacity = 1;
    }

    async function editarProducto(productoId) {
      try {
        const response = await fetch(`/api/productos/${productoId}`);
        if (!response.ok) {
          throw new Error('Error al obtener el producto');
        }
        const producto = await response.json();

        // Cargar los datos del producto en el modal
        document.getElementById('productoId').value = producto.id;
        document.getElementById('nombreProducto').value = producto.nombre;
        document.getElementById('descripcionProducto').value = producto
          .descripcion;
        document.getElementById('precioProducto').value = producto.precio;
        document.getElementById('cantidadProducto').value = producto.cantidad;
        document.getElementById('categoriaProducto').value = producto
          .categoria_id;

        // Mostrar el nombre de la imagen actual en el modal
        document.getElementById('imagenActual').textContent = producto.imagen ?
          `Imagen editada: ${producto.imagen}` :
          'No hay imagen disponible';

        // Mostrar el modal
        const modal = document.getElementById('productoModal');
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.style.opacity = 1;
      } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los datos del producto');
      }
    }

    function cerrarModal() {
      document.getElementById('imagenActual').textContent = '';
      document.getElementById('formularioProducto').reset();
      const modal = document.getElementById('productoModal');
      modal.style.display = 'none';
      modal.classList.remove('show');
      modal.style.opacity = 0;
    }

    document.getElementById('formularioProducto').addEventListener('submit',
    async function(event) {
        event.preventDefault();

        let producto = {
          nombre: document.getElementById('nombreProducto').value,
          descripcion: document.getElementById('descripcionProducto').value,
          precio: document.getElementById('precioProducto').value,
          cantidad: document.getElementById('cantidadProducto').value,
          categoria_id: document.getElementById('categoriaProducto').value
        };

        let fileInput = document.getElementById('imagenProducto');
        let file = fileInput.files[0];

        if (file) {
          let reader = new FileReader();
          reader.onloadend = async function() {
            let base64String = reader.result;
            producto.imagen = base64String;

            try {
              await enviarDatos(producto);
            } catch (error) {
              console.error('Error al enviar los datos:', error);
            }
          }
          reader.readAsDataURL(file);
        } else {
          try {
            await enviarDatos(producto);
          } catch (error) {
            console.error('Error al enviar los datos:', error);
          }
        }
      });

    async function enviarDatos(producto) {
      let productoId = document.getElementById('productoId').value;
      let url = productoId ? `/api/productos/${productoId}` : '/api/productos';
      let method = productoId ? 'PUT' : 'POST';

      try {
        const response = await fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            Producto: producto
          })
        });

        const data = await response.json();

        if (response.ok) {
          alert('Producto guardado con éxito');
          cerrarModal();
          window.location.reload();
        } else {
          throw new Error(data.message || 'Error al guardar el producto');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar el producto');
      }
    }

    async function deleteProducto(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        try {
          const response = await fetch(`/productos/${id}`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector(
                'meta[name="csrf-token"]').getAttribute('content'),
            },
          });

          if (!response.ok) {
            throw new Error('Error en la respuesta de la API');
          }

          const data = await response.json();

          if (data.mensaje) {
            alert(data.mensaje);
            window.location.reload(); // Recargar la página para ver los cambios
          } else {
            throw new Error('Error al eliminar el producto');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error al eliminar el producto');
        }
      }
    }
  </script>
@endsection
