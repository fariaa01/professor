<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Parcela extends Model
{
    protected $table = 'parcelas';

    protected $fillable = [
        'plano_id',
        'numero_parcela',
        'total_parcelas',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status_pagamento',
        'forma_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'numero_parcela' => 'integer',
        'total_parcelas' => 'integer',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function getParcelaFormatadaAttribute()
    {
        return "{$this->numero_parcela}/{$this->total_parcelas}";
    }

    public function getStatusVariantAttribute()
    {
        $variants = [
            'pago' => 'success',
            'pendente' => 'warning',
            'atrasado' => 'danger',
        ];

        return $variants[$this->status_pagamento] ?? 'secondary';
    }

    public function scopePendentes($query)
    {
        return $query->whereIn('status_pagamento', ['pendente', 'atrasado']);
    }

    public function scopePagas($query)
    {
        return $query->where('status_pagamento', 'pago');
    }

    public function scopeVencidasHoje($query)
    {
        return $query->where('data_vencimento', '<=', Carbon::today())
            ->where('status_pagamento', '!=', 'pago');
    }

    public function atualizarStatusAtrasado()
    {
        if ($this->status_pagamento === 'pendente' && $this->data_vencimento < Carbon::today()) {
            $this->update(['status_pagamento' => 'atrasado']);
        }
    }
}
