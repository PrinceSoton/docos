@push('scripts')
    <script>
        const roleSelect = document.getElementById('role');
        const roleFields = {
            mentor: document.getElementById('mentor-fields'),
            stagiaire: document.getElementById('stagiaire-fields'),
        };

        function updateRoleFields() {
            Object.entries(roleFields).forEach(([role, fields]) => {
                const active = roleSelect.value === role;
                fields.hidden = !active;
                fields.disabled = !active;
                fields.querySelectorAll('[name="date_debut"], [name="date_fin"]').forEach(input => input.required = active);
            });
        }

        roleSelect.addEventListener('change', updateRoleFields);
        updateRoleFields();
    </script>
@endpush
