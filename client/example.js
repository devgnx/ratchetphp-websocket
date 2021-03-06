var socket = new WebSocket('ws://localhost:8080/echo');

socket.onopen = function(event) {
  console.log('Conectado com: ' + event.currentTarget.URL);
};

socket.onerror = function(error) {
  console.error('Erro do WebSocket: ' + error);
};

socket.onclose = function(event) {
  console.log('Desconectado do WebSocket.');
};

socket.onmessage = function(event) {
  console.log('Dados recebidos:', event.data);
};

function send(data) {
  console.log('Enviando dados:', data);
  socket.send(data);
}

function close() {
  socket.close();
}
