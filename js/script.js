document.getElementById('iniciarMaquina').addEventListener('click', async () => {
    const response = await fetch('/start-server', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    });
    const result = await response.json();
    console.log(result);
  });