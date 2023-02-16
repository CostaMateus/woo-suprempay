Sugestões:

- API para consulta da bandeira do cartão
Facilidade no controle de quais bandeiras são ou não suportadas pela SupremPay
    parametros: card_number (6 primeiros digitos do cartão)
    retorno: brand (string), supported (boolean)

- API para obter o parcelamento
    parametros: brand, value (total), installment (número máximo de parcelas configurado pelo usuário no WC)
    retorno: installments (object)





SUPREM PAY

- API para consulta de bandeira do cartão    || como funciona a consulta de bandeira
- API para obter o número máximo de parcelas || como funciona a parte de parcelamento
- A validade do QRCode do PIX é fixa?        || como funciona a geração do qrcode do PIX ou retorna apenas uma chave?
- A validade do boleto é fixa?               || como funciona a geração do código de barras?

token_api         == fixo do
token-integration == do server

webhook pra receber atualização de status e confirmar com a consulta
