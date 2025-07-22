<?php

namespace App\Http\Requests;

use App\Constants\Messages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseRequest
 *
 * Base para todas as validações de requisição, com respostas JSON padronizadas.
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Mensagens de erro personalizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Lógica ao falhar na validação: retorna JSON com erros.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        $response = response()->json([
            'status'      => false,
            'message'     => Messages::MSG004,
            'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'errors'      => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new HttpResponseException($response);
    }

    /**
     * Lógica ao falhar na autorização: retorna JSON.
     *
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        $response = response()->json([
            'status'      => false,
            'message'     => 'Não autorizado.',
            'status_code' => Response::HTTP_FORBIDDEN,
        ], Response::HTTP_FORBIDDEN);

        throw new HttpResponseException($response);
    }
}
