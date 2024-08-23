@extends('layouts.app')

@section('content')
  <div class="container">
    @if (isset($error))
      <div class="alert alert-danger">{{ $error }}</div>
    @else
      <div class="row">
        <div class="col-6">
          <h1>Categorías</h1>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <button class="btn btn-primary" onclick="abrirModalCrearCategoria()">Nueva
            Categoría</button>
        </div>
      </div>

      <table class="table table-striped mt-4 table-bordered align-middle">
        <thead class="text-center">
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($categoriasData as $categoria)
            <tr>
              <td>{{ $categoria['nombre'] }}</td>
              <td>{{ $categoria['descripcion'] }}</td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm"
                  onclick="editarCategoria('{{ $categoria['id'] }}', '{{ $categoria['nombre'] }}', '{{ $categoria['descripcion'] }}')"><i
                    class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm"
                  onclick="deleteCategoria({{ $categoria['id'] }})"><i
                    class="fas fa-trash-alt"></i></button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="modal fade" id="categoriaModal" tabindex="-1" role="dialog"
    aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Administrar categoría</h5>
          <button type="button" class="btn-close"
            onclick="cerrarModalCategoria()" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formularioCategoria">
            @csrf
            <input type="hidden" id="categoriaId">
            <div class="form-group">
              <label for="nombreCategoria">Nombre</label>
              <input type="text" id="nombreCategoria" name="nombreCategoria"
                class="form-control" required>
            </div>
            <div class="form-group">
              <label for="descripcionCategoria">Descripción</label>
              <input type="text" id="descripcionCategoria"
                name="descripcionCategoria" class="form-control">
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
    function abrirModalCrearCategoria() {
      // Limpiar el formulario
      document.getElementById('formularioCategoria').reset();
      document.getElementById('categoriaId').value = '';

      const modal = document.getElementById('categoriaModal');
      modal.style.display = 'block';
      modal.classList.add('show');
      modal.style.opacity = 1;
    }

    function cerrarModalCategoria() {
      document.getElementById('formularioCategoria').reset();
      const modal = document.getElementById('categoriaModal');
      modal.style.display = 'none';
      modal.classList.remove('show');
      modal.style.opacity = 0;
    }

    async function editarCategoria(categoriaId) {
      try {
        const response = await fetch(`/api/categorias/${categoriaId}`);
        if (!response.ok) {
          throw new Error('Error al obtener la categoría');
        }
        const categoria = await response.json();

        // Cargar los datos de la categoría en el modal
        document.getElementById('categoriaId').value = categoria.id;
        document.getElementById('nombreCategoria').value = categoria.nombre;
        document.getElementById('descripcionCategoria').value = categoria
          .descripcion;

        // Mostrar el modal
        const modal = document.getElementById('categoriaModal');
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.style.opacity = 1;
      } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los datos de la categoría');
      }
    }
    document.getElementById('formularioCategoria').addEventListener('submit',
      async function(event) {
        event.preventDefault();

        let categoria = {
          nombre: document.getElementById('nombreCategoria').value,
          descripcion: document.getElementById('descripcionCategoria').value
        };

        try {
          await enviarDatos(categoria);
        } catch (error) {
          console.error('Error al enviar los datos:', error);
        }
      });

    async function enviarDatos(dato) {
      let id = document.getElementById('categoriaId').value;
      let url = id ? `/api/categorias/${id}` : '/api/categorias';
      let method = id ? 'PUT' : 'POST';

      try {
        const response = await fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            Categoria: dato
          })
        });

        const data = await response.json();

        if (response.ok) {
          alert('Categoría guardada con éxito');
          cerrarModalCategoria();
          window.location.reload();
        } else {
          throw new Error(data.message || 'Error al guardar la categoría');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar la categoría');
      }
    }

    async function deleteCategoria(id) {
      if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        try {
          const response = await fetch(`/categorias/${id}`, {
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
            throw new Error('Error al eliminar la categoría');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error al eliminar la categoría');
        }
      }
    }
  </script>

@endsection
