<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    /**
     * Ver el listado y el detalle: basta con el permiso general.
     * El TenantScope ya se encarga de que solo vea las de su empresa.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('purchase-orders.view');
    }

    public function view(User $user, PurchaseOrder $order): bool
    {
        return $user->can('purchase-orders.view');
    }

    public function create(User $user): bool
    {
        return $user->can('purchase-orders.create');
    }

    /**
     * Aprobar/rechazar: es una revisión de la solicitud de otro,
     * así que NO se valida "dueño del registro" — solo el permiso.
     * Solo tiene sentido sobre una orden pendiente.
     */
    public function approve(User $user, PurchaseOrder $order): bool
    {
        return $user->can('purchase-orders.approve') && $order->status === 'pendiente';
    }

    public function reject(User $user, PurchaseOrder $order): bool
    {
        return $user->can('purchase-orders.approve') && $order->status === 'pendiente';
    }

    /**
     * Recibir: solo Administrador/quien tenga el permiso,
     * y solo si ya está aprobada.
     */
    public function receive(User $user, PurchaseOrder $order): bool
    {
        return $user->can('purchase-orders.receive') && $order->status === 'aprobada';
    }

    /**
     * Cancelar: aquí sí importa quién es el dueño.
     * Administrador cancela cualquiera; Comprador solo las suyas.
     */
    public function cancel(User $user, PurchaseOrder $order): bool
    {
        if (! in_array($order->status, ['pendiente', 'aprobada'])) {
            return false;
        }

        if ($user->hasRole('Administrador')) {
            return true;
        }

        return $user->id === $order->user_id && $user->can('purchase-orders.cancel');
    }
}