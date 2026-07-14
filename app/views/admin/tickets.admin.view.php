<!-- comme d'habitude erreur phpsur la variable due a l'ide -->
<?php /** @var array $listeTickets */ ?>


<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Nom</th>
            <th>Sujet</th>
            <th>Message</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listeTickets as $ticket): ?>
            <tr>
                <td><?= $ticket['date_creation'] ?></td>
                <td><?= htmlspecialchars($ticket['nom']) ?></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($ticket['sujet']) ?></span></td>

                <td class="text-wrap text-break" style="max-width: 300px;">
                    <div style="max-height: 80px; max-width: 300px; overflow-y: auto; white-space: pre-wrap;">
                        <?= htmlspecialchars($ticket['message']) ?>
                    </div>
                </td> ```

                <td>
                    <form action="index.php?page=delete-tickets-admin" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?');">
                        <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>