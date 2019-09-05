<?php
namespace ApiV1Bundle\Entity\Interfaces;

interface UsuarioSyncInterface
{

    public function edit($id, $params);

    public function delete($id);
}
