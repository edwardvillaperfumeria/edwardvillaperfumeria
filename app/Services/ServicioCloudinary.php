<?php

namespace App\Services;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class ServicioCloudinary
{
    /**
     * instancia de la API de subida de Cloudinary.
     */
    protected $apiSubida;

    public function __construct()
    {
        // Se configura el SDK usando la URL del .env
        Configuration::instance(env('CLOUDINARY_URL'));
        $this->apiSubida = new UploadApi();
    }

    /**
     * Sube una imagen a Cloudinary.
     * 
     * @param string $rutaArchivo Ruta temporal del archivo.
     * @param string $carpeta Carpeta de destino en Cloudinary.
     * @return string URL segura de la imagen subida.
     */
    public function subirImagen($rutaArchivo, $carpeta = 'productos')
    {
        $resultado = $this->apiSubida->upload($rutaArchivo, [
            'folder' => $carpeta,
            'resource_type' => 'image',
            'delivery_type' => 'upload'
        ]);

        return $resultado['secure_url'];
    }

    /**
     * Elimina una imagen de Cloudinary.
     * 
     * @param string $urlImagen URL completa de la imagen.
     * @return bool
     */
    public function eliminarImagen($urlImagen)
    {
        if (!$urlImagen) return false;

        // Extraer el public_id de la URL
        // Ejemplo: https://res.cloudinary.com/dpa2no26p/image/upload/v1740332824/productos/id.jpg
        $partes = explode('/', $urlImagen);
        $nombreArchivo = end($partes);
        $carpeta = prev($partes);
        
        $publicId = $carpeta . '/' . pathinfo($nombreArchivo, PATHINFO_FILENAME);

        try {
            $this->apiSubida->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            \Log::error("Error eliminando imagen de Cloudinary: " . $e->getMessage());
            return false;
        }
    }
}
