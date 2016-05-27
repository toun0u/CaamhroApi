<!--
Liste des éléments dispo
{if $planning|@count > 0}
	{foreach from=$planning key=k item=planning}
		{$planning.id}
		{$planning.libelle}
		{$planning.description}
		{$planning.datejour}
		{$planning.datefin}
		{$planning.heuredeb}
		{$planning.heurefin}
		{$planning.type}
		{$planning.address}
		{$planning.cp}
		{$planning.city}
		{$planning.organizer}
	{/foreach}
{/if}
-->